// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

Cypress.Commands.add('loginAsAdmin', () => {
  cy.visit('/login');
  cy.get('input[name=email]').type('admin@camporeal.com');
  cy.get('input[name=password]').type('admin123');
  cy.get('button[type=submit]').click();
  cy.url().should('include', '/dashboard');
});

Cypress.Commands.add('criarUsuario', (nome, sobrenome) => {
  cy.visit('/usuarios/create');
  cy.get('input[name=nome]').type(nome);
  cy.get('input[name=sobrenome]').type(sobrenome);
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

  
Cypress.Commands.add('deletarUsuario', (nome, sobrenome) => {
  cy.visit('/usuarios');
  
  // Tenta encontrar o usuário, se não encontrar, apenas continua
  cy.get('tbody tr').each(($row) => {
    if ($row.text().includes(`${nome} ${sobrenome}`)) {
      cy.wrap($row).within(() => {
        cy.get('button.btn-danger').click();
      });
      cy.on('window:confirm', () => true);
      cy.contains('Usuário excluído com sucesso!').should('be.visible');
    }
  });
});
