describe('Usuário', () => {

  beforeEach(() => {
    cy.loginAsAdmin();
  });

  it('deve cadastrar usuário com dados válidos', () => {
    cy.visit('/usuarios/create');
    cy.get('input[name=nome]').type('Cleiton');
    cy.get('input[name=sobrenome]').type('Rastra');
    cy.get('input[name=data_nascimento]').type('1990-01-01');
    cy.get('input[name=cep]').type('12345-678');
    cy.get('input[name=rua]').type('Rua Teste');
    cy.get('input[name=numero]').type('123');
    cy.get('input[name=bairro]').type('Centro');
    cy.get('input[name=cidade]').type('São Paulo');
    cy.get('input[name=estado]').type('SP');
    cy.get('select[name=tipo_usuario]').select('recepcionista');
    cy.get('input[name=senha]').type('senha123');
    cy.get('input[name=senha_confirmation]').type('senha123');
    cy.get('button[type=submit]').contains('Cadastrar').click();
    cy.contains('Usuário cadastrado com sucesso!').should('be.visible');
  });

  it('não deve cadastrar usuário com dados inválidos', () => {
    cy.visit('/usuarios/create');
    cy.get('button[type=submit]').contains('Cadastrar').click();
    cy.contains('O campo nome é obrigatório.').should('be.visible');
  });

  it('não deve cadastrar usuário duplicado', () => {
    // Tenta cadastrar o mesmo usuário novamente
    cy.visit('/usuarios/create');
    cy.get('input[name=nome]').type('Cleiton');
    cy.get('input[name=sobrenome]').type('Rastra');
    cy.get('input[name=data_nascimento]').type('1990-01-01');
    cy.get('input[name=cep]').type('12345-678');
    cy.get('input[name=rua]').type('Rua Teste');
    cy.get('input[name=numero]').type('123');
    cy.get('input[name=bairro]').type('Centro');
    cy.get('input[name=cidade]').type('São Paulo');
    cy.get('input[name=estado]').type('SP');
    cy.get('select[name=tipo_usuario]').select('recepcionista');
    cy.get('input[name=senha]').type('senha123');
    cy.get('input[name=senha_confirmation]').type('senha123');
    cy.get('button[type=submit]').contains('Cadastrar').click();
    cy.contains('Já existe um usuário cadastrado com este nome e sobrenome.').should('be.visible');
  });

  it('deve atualizar um usuário', () => {
    cy.visit('/usuarios');
    cy.contains('Cleiton Rastra').parent('tr').within(() => {
      cy.get('a.btn-warning').click();
    });
    cy.get('input[name=sobrenome]').clear().type('Santos');
    cy.get('button[type=submit]').contains('Atualizar').click();
    cy.contains('Usuário atualizado com sucesso!').should('be.visible');
  });

  it('não deve permitir edição para nome já existente', () => {
    cy.visit('/usuarios');
    cy.contains('Cleiton Santos').parent('tr').within(() => {
      cy.get('a.btn-warning').click();
    });
    cy.get('input[name=nome]').clear().type('Admin');
    cy.get('input[name=sobrenome]').clear();
    cy.get('button[type=submit]').contains('Atualizar').click();
    cy.contains('Já existe um usuário cadastrado com este nome e sobrenome.').should('be.visible');
  });

  it('deve excluir um usuário', () => {
    cy.visit('/usuarios');
    cy.contains('Cleiton Santos').parent('tr').within(() => {
      cy.get('button.btn-danger').click();
    });
    cy.on('window:confirm', () => true);
    cy.contains('Usuário excluído com sucesso!').should('be.visible');
  });

  it('deve exibir modal de erro ao falhar no cadastro (simulação)', () => {
    // Intercepta a requisição POST e força um erro 500
    cy.intercept('POST', '/usuarios', {
        statusCode: 500,
        body: {
            success: false,
            message: 'Ocorreu um erro ao salvar o usuário. Tente novamente.'
        }
    }).as('postUsuario');

    cy.visit('/usuarios/create');
    cy.get('input[name=nome]').type('Teste');
    cy.get('input[name=sobrenome]').type('Erro');
    cy.get('input[name=data_nascimento]').type('1990-01-01');
    cy.get('input[name=cep]').type('12345-678');
    cy.get('input[name=rua]').type('Rua Teste');
    cy.get('input[name=numero]').type('123');
    cy.get('input[name=bairro]').type('Centro');
    cy.get('input[name=cidade]').type('São Paulo');
    cy.get('input[name=estado]').type('SP');
    cy.get('select[name=tipo_usuario]').select('recepcionista');
    cy.get('input[name=senha]').type('senha123');
    cy.get('input[name=senha_confirmation]').type('senha123');
    cy.get('button[type=submit]').contains('Cadastrar').click();
    
    // Espera a requisição ser interceptada
    cy.wait('@postUsuario');
    
    // Verifica se o modal de erro está visível
    cy.get('#errorToast').should('be.visible');
    cy.get('#errorMessage').should('contain', 'Ocorreu um erro ao salvar o usuário');
});
});