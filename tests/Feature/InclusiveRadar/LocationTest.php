<?php

namespace Tests\Feature\InclusiveRadar;

use App\Models\User;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->institution = Institution::factory()->create(['is_active' => true]);
    }

    /**
     * Objetivo: Redirecionar visitantes (não logados) para a tela de login ao tentar acessar a listagem.
     * Cenário: Triste (Acesso sem autenticação).
     */
    public function test_guest_cannot_access_locations_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.locations.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    /**
     * Objetivo: Impedir que usuários sem privilégios de administrador acessem a listagem de localizações.
     * Cenário: Triste (Permissão insuficiente / Autorização).
     */
    public function test_non_admin_cannot_access_locations_index()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => false]);

        // Act
        $response = $this->actingAs($user)
            ->get(route('inclusive-radar.locations.index'));

        // Assert
        $response->assertStatus(403);
    }

    /**
     * Objetivo: Validar se o administrador consegue visualizar a listagem de localizações.
     * Cenário: Feliz (Visualização de dados).
     */
    public function test_it_can_list_locations()
    {
        // Arrange
        Location::factory()->count(3)->create([
            'institution_id' => $this->institution->id
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('locations');

        // Opcional
        $locations = $response->viewData('locations');
        $this->assertCount(3, $locations);
    }

    /**
     * Objetivo: Validar a criação e persistência de uma nova localização com dados válidos.
     * Cenário: Feliz (Fluxo principal de armazenamento).
     */
    public function test_it_creates_a_new_location_with_valid_data()
    {
        // Arrange
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Rampa de Acesso Bloco A',
            'type'           => 'Acessibilidade',
            'latitude'       => -14.2312,
            'longitude'      => -42.7123,
            'is_active'      => true,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.locations.index'));

        $this->assertDatabaseHas('locations', [
            'name'           => 'Rampa de Acesso Bloco A',
            'institution_id' => $this->institution->id,
            'latitude'       => -14.2312,
            'is_active'      => true
        ]);
    }

    /**
     * Objetivo: Garantir que latitude e longitude sejam campos obrigatórios.
     * Cenário: Triste (Falha de validação de campos nulos).
     */
    public function test_it_fails_if_coordinates_are_missing()
    {
        // Arrange
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Sem GPS',
            'latitude'       => null,
            'longitude'      => null,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->from(route('inclusive-radar.locations.create'))
            ->post(route('inclusive-radar.locations.store'), $data);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertDatabaseMissing('locations', ['name' => 'Local Sem GPS']);
    }

    /**
     * Objetivo: Validar que o sistema rejeite latitudes (>90 ou <-90) e longitudes (>180 ou <-180) inválidas.
     * Cenário: Triste (Validação de limites geográficos).
     */
    public function test_it_fails_if_coordinates_are_out_of_range()
    {
        // Arrange
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local no Espaço',
            'latitude'       => 95.0,
            'longitude'      => 185.0,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data);

        // Assert
        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertDatabaseMissing('locations', ['name' => 'Local no Espaço']);
    }

    /**
     * Objetivo: Validar se o administrador consegue atualizar os dados de uma localização existente.
     * Cenário: Feliz (Persistência de dados).
     */
    public function test_it_can_update_a_location()
    {
        // Arrange
        $location = Location::factory()->create(['name' => 'Nome Original']);

        $newData = [
            'institution_id' => $location->institution_id,
            'name'           => 'Nome Alterado',
            'latitude'       => -14.0000,
            'longitude'      => -42.0000,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put(route('inclusive-radar.locations.update', $location), $newData);

        // Assert
        $response->assertRedirect(route('inclusive-radar.locations.index'));

        $this->assertDatabaseHas('locations', [
            'id'   => $location->id,
            'name' => 'Nome Alterado'
        ]);

        // Opcional
        $this->assertDatabaseMissing('locations', ['name' => 'Nome Original']);
    }

    /**
     * Objetivo: Garantir que o administrador consiga realizar a exclusão lógica (Soft Delete).
     * Cenário: Feliz (Integridade de dados).
     */
    public function test_it_can_soft_delete_a_location()
    {
        // Arrange
        $location = Location::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->delete(route('inclusive-radar.locations.destroy', $location));

        // Assert
        $response->assertRedirect();
        $this->assertSoftDeleted('locations', [
            'id' => $location->id
        ]);
    }

    /** * Objetivo: Testar o filtro de busca por nome da instituição vinculada.
     * Cenário: Feliz (Filtro por Relacionamento).
     */
    public function test_it_filters_locations_by_institution_name()
    {
        // Arrange
        $instA = Institution::factory()->create(['name' => 'Campus Guanambi']);
        $instB = Institution::factory()->create(['name' => 'Campus Salvador']);

        Location::factory()->create(['name' => 'Biblioteca', 'institution_id' => $instA->id]);
        Location::factory()->create(['name' => 'Laboratório', 'institution_id' => $instB->id]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index', ['institution_name' => 'Guanambi']));

        // Assert
        $response->assertStatus(200);

        $locations = $response->viewData('locations');

        $this->assertTrue($locations->contains('name', 'Biblioteca'), 'A Biblioteca deveria estar nos resultados.');
        $this->assertFalse($locations->contains('name', 'Laboratório'), 'O Laboratório não deveria estar nos resultados filtrados.');
    }

    /**
     * Objetivo: Validar se a requisição AJAX retorna apenas o conteúdo parcial (sem o layout completo).
     * Cenário: Feliz (Integração Frontend/Datatables).
     */
    public function test_it_returns_ajax_partial_successfully()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index'), [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertStringNotContainsString('<html', $response->getContent());
        $this->assertStringNotContainsString('<body', $response->getContent());
    }

    /**
     * Objetivo: Validar que o campo 'type' não aceite strings maiores que o limite permitido.
     * Cenário: Triste (Falha de validação).
     */
    public function test_it_fails_if_type_exceeds_max_length()
    {
        // Arrange
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Grande',
            'type'           => str_repeat('A', 101),
            'latitude'       => -14.0000,
            'longitude'      => -42.0000,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data);

        // Assert
        $response->assertSessionHasErrors(['type']);
        $this->assertDatabaseMissing('locations', ['name' => 'Local Grande']);
    }

    /**
     * Objetivo: Garantir que o campo is_active seja falso por padrão se omitido no request.
     * Cenário: Feliz (Valor default/Tratamento de input).
     */
    public function test_it_sets_is_active_to_false_when_missing()
    {
        // Arrange
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Inativo',
            'latitude'       => -14.0,
            'longitude'      => -42.0,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('locations', [
            'name'      => 'Local Inativo',
            'is_active' => false
        ]);
    }

    /** * Objetivo: Testar se o filtro de status (is_active) funciona corretamente via query string.
     * Cenário: Feliz (Filtro '1' para ativos, '0' para inativos).
     */
    public function test_it_filters_locations_by_active_status()
    {
        // Arrange
        $ativo = Location::factory()->create(['name' => 'Local Ativo', 'is_active' => true]);
        $inativo = Location::factory()->create(['name' => 'Local Inativo', 'is_active' => false]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index', ['is_active' => '1']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($ativo->name);
        $response->assertDontSee($inativo->name);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index', ['is_active' => '0']));

        // Assert
        $response->assertSee($inativo->name);
        $response->assertDontSee($ativo->name);
    }

    /**
     * Integridade do Formulário: Verifica se a página de criação carrega
     * apenas instituições aptas (ativas) para receber novos locais.
     */
    public function test_it_displays_the_create_view_with_active_institutions()
    {
        // Arrange
        Institution::factory()->create(['is_active' => true, 'name' => 'Campus Ativo']);
        Institution::factory()->create(['is_active' => false, 'name' => 'Campus Desativado']);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.create'));

        // Assert
        $response->assertStatus(200);
        $institutions = $response->viewData('institutions');
        $this->assertTrue($institutions->contains('name', 'Campus Ativo'));
        $this->assertFalse($institutions->contains('name', 'Campus Desativado'));
    }

    /**
     * Exibição Detalhada: Garante que a página de visualização individual
     * exibe os dados corretos do ponto de referência.
     */
    public function test_it_displays_the_show_view()
    {
        // Arrange
        $location = Location::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.show', $location));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('location', $location);
    }

    /**
     * Preparação de Edição: Verifica se o formulário de edição recupera os dados
     * do local e a listagem de instituições disponíveis.
     */
    public function test_it_displays_the_edit_view()
    {
        // Arrange
        $location = Location::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.edit', $location));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('location', $location);
        $response->assertViewHas('institutions');
    }

    /**
     * Pesquisa Textual: Garante que a busca por nome no índice de locais
     * retorna apenas os registros correspondentes.
     */
    public function test_it_filters_locations_by_name()
    {
        // Arrange
        Location::factory()->create(['name' => 'Biblioteca Central']);
        Location::factory()->create(['name' => 'Ginásio de Esportes']);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index', ['name' => 'Biblioteca']));

        // Assert
        $response->assertSee('Biblioteca Central');
        $response->assertDontSee('Ginásio de Esportes');
    }
}
