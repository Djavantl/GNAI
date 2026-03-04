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

        // Arrange: Preparamos o admin e uma instituição ativa para os vínculos
        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->institution = Institution::factory()->create(['is_active' => true]);
    }

    /** * Objetivo: Impedir acesso de visitantes à gestão de locais.
     * Cenário: Triste (Sem Autenticação).
     */
    public function test_guest_cannot_access_locations_index()
    {
        $this->get(route('inclusive-radar.locations.index'))
            ->assertRedirect('auth/login');
    }

    /** * Objetivo: Impedir que usuários comuns acessem rotas administrativas.
     * Cenário: Triste (Sem Autorização).
     */
    public function test_non_admin_cannot_access_locations_index()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('inclusive-radar.locations.index'))
            ->assertStatus(403);
    }

    /** * Objetivo: Validar se a listagem exibe os locais cadastrados.
     * Cenário: Feliz.
     */
    public function test_it_can_list_locations()
    {
        Location::factory()->count(3)->create(['institution_id' => $this->institution->id]);

        $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index'))
            ->assertStatus(200)
            ->assertViewHas('locations');
    }

    /** * Objetivo: Criar um local com sucesso enviando todos os dados obrigatórios.
     * Cenário: Feliz.
     */
    public function test_it_creates_a_new_location_with_valid_data()
    {
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Rampa de Acesso Bloco A',
            'type'           => 'Acessibilidade',
            'latitude'       => -14.2312,
            'longitude'      => -42.7123,
            'is_active'      => true,
        ];

        $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data)
            ->assertRedirect(route('inclusive-radar.locations.index'));

        $this->assertDatabaseHas('locations', [
            'name' => 'Rampa de Acesso Bloco A',
            'latitude' => -14.2312
        ]);
    }

    /** * Objetivo: Garantir que o sistema REJEITE a criação sem as coordenadas (Obrigatórias).
     * Cenário: Triste (Erro de Validação).
     */
    public function test_it_fails_if_coordinates_are_missing()
    {
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Sem GPS',
            'latitude'       => null, // Deixando nulo propositalmente
            'longitude'      => null,
        ];

        $this->actingAs($this->adminUser)
            ->from(route('inclusive-radar.locations.create'))
            ->post(route('inclusive-radar.locations.store'), $data)
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /** * Objetivo: Validar que coordenadas fora do padrão geográfico são rejeitadas.
     * Cenário: Triste (Validação de Limite).
     */
    public function test_it_fails_if_coordinates_are_out_of_range()
    {
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local no Espaço',
            'latitude'       => 95.0,  // Max 90
            'longitude'      => 185.0, // Max 180
        ];

        $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data)
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /** * Objetivo: Verificar a atualização de um local existente.
     * Cenário: Feliz.
     */
    public function test_it_can_update_a_location()
    {
        $location = Location::factory()->create(['name' => 'Nome Original']);

        $newData = [
            'institution_id' => $location->institution_id,
            'name'           => 'Nome Alterado',
            'latitude'       => -14.0000,
            'longitude'      => -42.0000,
        ];

        $this->actingAs($this->adminUser)
            ->put(route('inclusive-radar.locations.update', $location), $newData)
            ->assertRedirect(route('inclusive-radar.locations.index'));

        $this->assertDatabaseHas('locations', ['name' => 'Nome Alterado']);
    }

    /** * Objetivo: Testar a exclusão lógica (Soft Delete).
     * Cenário: Feliz.
     */
    public function test_it_can_soft_delete_a_location()
    {
        $location = Location::factory()->create();

        $this->actingAs($this->adminUser)
            ->delete(route('inclusive-radar.locations.destroy', $location));

        $this->assertSoftDeleted('locations', ['id' => $location->id]);
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

    /** * Objetivo: Validar se a resposta via AJAX retorna apenas o conteúdo da tabela.
     * Cenário: Feliz (Técnico).
     */
    public function test_it_returns_ajax_partial_successfully()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index'), [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200);
        // Garante que não carregou a estrutura completa do HTML (header, body, etc)
        $this->assertStringNotContainsString('<html', $response->getContent());
    }

    public function test_it_fails_if_type_exceeds_max_length()
    {
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Grande',
            'type'           => str_repeat('A', 101),
            'latitude'       => -14.0000,
            'longitude'      => -42.0000,
        ];

        $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data)
            ->assertSessionHasErrors(['type']);
    }

    public function test_it_sets_is_active_to_false_when_missing()
    {
        $data = [
            'institution_id' => $this->institution->id,
            'name'           => 'Local Inativo',
            'latitude'       => -14.0,
            'longitude'      => -42.0,
        ];

        $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.locations.store'), $data);

        $this->assertDatabaseHas('locations', [
            'name' => 'Local Inativo',
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

        // Act: Filtrando por ATIVOS (is_active=1)
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.locations.index', ['is_active' => '1']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($ativo->name);
        $response->assertDontSee($inativo->name);

        // Act: Filtrando por INATIVOS (is_active=0)
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
