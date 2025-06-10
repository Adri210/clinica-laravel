describe('Autenticação', () => {

  it('não deve fazer login com credenciais inválidas', () => {
    cy.visit('/login');
    cy.get('input[name=email]').type('admin@teste.com');
    cy.get('input[name=password]').type('senhaerrada');
    cy.get('button[type=submit]').click();
    cy.contains('E-mail ou senha incorretos').should('be.visible');
  });

  it('deve fazer login com credenciais válidas', () => {
    cy.visit('/login');
    cy.get('input[name=email]').type('admin@camporeal.com');
    cy.get('input[name=password]').type('admin123');
    cy.get('button[type=submit]').click();
    cy.url().should('include', '/dashboard');
  });


  it('deve fazer logout', () => {
    cy.loginAsAdmin();
    cy.visit('/dashboard');
    cy.contains('button', 'Logout').click();
    cy.url().should('include', '/login');
  });
});