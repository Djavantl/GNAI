<?php

namespace Tests\Feature\InclusiveRadar;

use App\Models\User;
use App\Models\InclusiveRadar\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        $this->adminUser = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    /** * Objetivo: Garantir que usuários não logados não acessem a gestão.
     * Cenário: Triste (Sem Autenticação).
     */
    public function test_guest_cannot_access_institution_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.institutions.index'));

        // Assert
        $response->assertRedirect('auth/login');
    }

    /** * Objetivo: Garantir que usuários logados sem permissão de admin sejam barrados.
     * Cenário: Triste (Sem Autorização).
     */
    public function test_non_admin_user_cannot_access_institution_index()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => false]);

        // Act
        $response = $this->actingAs($user)
            ->get(route('inclusive-radar.institutions.index'));

        // Assert
        $response->assertStatus(403);
    }

    /** * Objetivo: Verificar se a listagem de instituições carrega para o admin.
     * Cenário: Feliz.
     */
    public function test_it_can_list_institutions()
    {
        // Arrange
        Institution::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('institutions');
    }

    /** * Objetivo: Validar a criação de uma instituição com dados válidos.
     * Cenário: Feliz.
     */
    public function test_it_creates_a_new_institution_with_valid_data()
    {
        // Arrange
        $data = [
            'name' => 'Instituto GNAI',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.22,
            'longitude' => -42.77,
            'is_active' => true,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post(route('inclusive-radar.institutions.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.institutions.index'));
        $this->assertDatabaseHas('institutions', ['name' => 'Instituto GNAI']);
    }

    /** * Objetivo: Garantir que o sistema barra a criação sem campos obrigatórios.
     * Cenário: Triste (Erro de Validação).
     */
    public function test_it_fails_validation_if_required_fields_are_missing()
    {
        // Arrange
        $data = [
            'name' => '',
            'latitude' => '',
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->from(route('inclusive-radar.institutions.create'))
            ->post(route('inclusive-radar.institutions.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.institutions.create'));
        $response->assertSessionHasErrors(['name', 'latitude', 'longitude', 'city', 'state']);
    }

    /** * Objetivo: Testar a regra de negócio quando o Service identifica duplicidade.
     * Cenário: Triste (Regra de Negócio).
     */
    public function test_it_returns_error_if_service_fails_to_store_duplicate()
    {
        // Arrange
        $existing = Institution::factory()->create(['name' => 'Instituição Duplicada']);
        $data = [
            'name' => 'Instituição Duplicada',
            'city' => $existing->city,
            'state' => $existing->state,
            'latitude' => $existing->latitude,
            'longitude' => $existing->longitude,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->from(route('inclusive-radar.institutions.create'))
            ->post(route('inclusive-radar.institutions.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.institutions.create'));
        $response->assertSessionHas('error', 'Já existe uma instituição cadastrada com esses dados.');
    }

    /** * Objetivo: Validar a atualização de dados de uma instituição.
     * Cenário: Feliz.
     */
    public function test_it_can_update_an_institution()
    {
        // Arrange
        $institution = Institution::factory()->create(['name' => 'Antigo Nome']);
        $newData = [
            'name' => 'Novo Nome',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.00,
            'longitude' => -42.00,
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put(route('inclusive-radar.institutions.update', $institution), $newData);

        // Assert
        $response->assertRedirect(route('inclusive-radar.institutions.index'));
        $this->assertDatabaseHas('institutions', ['id' => $institution->id, 'name' => 'Novo Nome']);
    }

    /** * Objetivo: Confirmar que a deleção (Soft Delete) funciona.
     * Cenário: Feliz.
     */
    public function test_it_can_soft_delete_an_institution()
    {
        // Arrange
        $institution = Institution::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->delete(route('inclusive-radar.institutions.destroy', $institution));

        // Assert
        $response->assertRedirect(route('inclusive-radar.institutions.index'));
        $this->assertSoftDeleted('institutions', ['id' => $institution->id]);
    }

    /** * Objetivo: Garantir que o filtro por nome funciona corretamente.
     * Cenário: Feliz (Busca específica).
     */
    public function test_it_filters_institutions_by_name()
    {
        // Arrange
        Institution::factory()->create(['name' => 'IFBA Campus Guanambi']);
        Institution::factory()->create(['name' => 'UNEB Campus VI']);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.index', ['name' => 'IFBA']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('IFBA Campus Guanambi');
        $response->assertDontSee('UNEB Campus VI');
    }

    /** * Objetivo: Garantir que o filtro de status (ativo/inativo) funciona.
     * Cenário: Feliz.
     */
    public function test_it_filters_institutions_by_status()
    {
        // Arrange
        Institution::factory()->create(['name' => 'Ativa', 'is_active' => true]);
        Institution::factory()->create(['name' => 'Inativa', 'is_active' => false]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.index', ['is_active' => '1']));

        // Assert
        $response->assertSee('Ativa');
        $response->assertDontSee('Inativa');
    }

    /** * Objetivo: Validar a busca por localização (Cidade ou Estado).
     * Cenário: Feliz.
     */
    public function test_it_filters_institutions_by_location()
    {
        // Arrange
        Institution::factory()->create(['name' => 'Local A', 'city' => 'Guanambi']);
        Institution::factory()->create(['name' => 'Local B', 'city' => 'Salvador']);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.index', ['location' => 'Guanambi']));

        // Assert
        $response->assertSee('Local A');
        $response->assertDontSee('Local B');
    }

    /**
     * Exibição do Formulário de Cadastro: Garante que a rota de criação
     * está acessível e renderiza o formulário correto.
     */
    public function test_it_displays_the_create_view()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.inclusive-radar.institutions.create');
    }

    /**
     * Visualização de Detalhes: Verifica se a página de exibição individual
     * carrega corretamente a instituição e seus relacionamentos.
     */
    public function test_it_displays_the_show_view()
    {
        // Arrange
        $institution = Institution::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.show', $institution));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.inclusive-radar.institutions.show');
        $response->assertViewHas('institution');
    }

    /**
     * Exibição do Formulário de Edição: Garante que os dados existentes
     * são carregados corretamente para modificação.
     */
    public function test_it_displays_the_edit_view()
    {
        // Arrange
        $institution = Institution::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.edit', $institution));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.inclusive-radar.institutions.edit');
        $response->assertViewHas('institution');
    }

    /**
     * Resposta AJAX: Verifica se o controlador retorna apenas a partial da tabela
     * em requisições assíncronas.
     */
    public function test_it_returns_partial_table_view_on_ajax_request()
    {
        // Arrange
        Institution::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get(route('inclusive-radar.institutions.index'), [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.inclusive-radar.institutions.partials.table');
        $response->assertViewHas('institutions');
    }
}
