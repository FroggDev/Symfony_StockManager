Feature: Creating An account
    # not technic action
  In order to create an account & change its password
  # utilisateur
  As a visitor
  # technic action
  I need to register an account, validate it, then i can try to recover my password.
  Once done, i can make some more extra security stress tests.

    #################
    # Reset Context #
    #################

  Scenario: ContextReset
    Given I recreate database
    Then I delete old screenshots

    ##################
    # Resize Browser #
    ##################

  @javascript
  Scenario: ResizeBrowser
    # going to register page from button from home
    Given I am on "/"
    # set windows size and reset
    Then I set browser window size to "450" x "850"

    ############
    # Register #
    ############

  @javascript
  Scenario: Register
    # going to register page from button from home
    Given I am on "/"
    And I should see "Gestionnaire de stock en ligne"
    Then I follow "register-button"

    # test register page
    # test if only firstname is filled should not validate
    Then I should see "Création de compte"
    When I fill in "user_firstname" with "FroggTest"
    And I press "S'enregistrer"
    Then I should see "Création de compte"
    When I fill in "user_lastname" with "FroggTest"
    And I press "S'enregistrer"
    Then I should see "Création de compte"
    When I fill in "user_email" with "test@frogg.fr"
    And I press "S'enregistrer"
    Then I should see "Création de compte"
    # test full validation & try to create account
    When I fill in "user_password" with "FroggTest"
    And I press "S'enregistrer"
    Then I should see "Un mail de validation de création de compte vous a été envoyé, vous trouverez un lien dans ce mail pour valider votre compte."
    And User "test@frogg.fr" should be "Disabled" in database
    # DEMO TEMPORIZATION
    Then Wait for demo

    ###############################
    # Register Already Registered #
    ###############################

  @javascript
  Scenario: RegisterAsAlreadyRegistered
    # going to register page from login button from home
    Given I am on "/"
    Then I should see "Gestionnaire de stock en ligne"
    And I follow "security_connexion_small"

    Then I should see "S'inscrire"
    And I follow "security_register"

    # test register page
    Then I should see "Création de compte"
    When I fill in "user_firstname" with "FroggTest"
    And I fill in "user_lastname" with "FroggTest"
    And I fill in "user_email" with "test@frogg.fr"
    And I fill in "user_password" with "FroggTest"
    And I press "S'enregistrer"

    And I take a screenshot "register-form-already-exist.png"
    # DEMO TEMPORIZATION
    Then Wait for demo

    ##########################
    # Login as disabled user #
    ##########################

  @javascript
  Scenario: LoginAsDisabledUser
    # going to register page from login button from home
    Given I am on "/"
    Then I should see "Gestionnaire de stock en ligne"
    And I follow "security_connexion_small"

    # test connexion page
    Then I should see "Se connecter"
    When I fill in "_username" with "test@frogg.fr"
    And I fill in "_password" with "FroggTest"
    And I press "button_security_connexion"

    Then I should see "Ce compte n'a pas encore été activé, vous devez avoir reçu un mail de validation dans lequel un lien d'activation est disponbile"
    ## ==> Screenshot
    And I take a screenshot "register-form-already-exist.png"
    # DEMO TEMPORIZATION
    Then Wait for demo

    ###############################################################
    # Registration Validation invalid Token with disabled account #
    ###############################################################

  @javascript
  Scenario: RegisterValidationInvalidToken
    Given I am on "/compte/confirmation.html?email=test@frogg.fr&token=fakerandomtoken"
    Then I should see "Requête invalide"
    # DEMO TEMPORIZATION
    Then Wait for demo

    ###########################
    # Registration Validation #
    ###########################

  @javascript
  Scenario: RegistrationValidation
    # going to mail trap to test mail
    Given I am on "https://mailtrap.io/signin"
    Then I should see "Log In"
    When I fill in "user_email" with "mailtrap@frogg.fr"
    And I fill in "user_password" with "nimpmailtrap"
    And I press "commit"
    When I wait "3" sec
    Then I should see "My Inboxes"
    And I follow "Demo inbox"
    When I wait "3" sec
    And I click on selector "LI.email.new-email"
    When I wait "3" sec

    Then I should see "Confirmation de création de compte"
    And I open an iframe on Selector "IFRAME.flex-item" from server "https://mailtrap.io"
    ## ==> Screenshot
    And I take a screenshot "register-confirmation-mail.png"
    When I wait "3" sec
    Then I should see "Validation de la création du compte"
    And I follow "Valider"

    # IMPORTANT
    Then I switch tab
    And I should see "Votre compte a été validé, vous pouvez maintenant vous connecter à l'application"
    And Input "#_username" should be fill with "test@frogg.fr"
    # test connexion page
    When I wait "3" sec
    And I fill in "_username" with "test@frogg.fr"
    And I fill in "_password" with "FroggTest"
    And I press "button_security_connexion"

    Then I should see "Mon Stock"
    And cookie "user" should be fill with "test@frogg.fr"
    And User "test@frogg.fr" should be "Enabled" in database
    # DEMO TEMPORIZATION
    Then Wait for demo

    # set windows size and reset
    Then I set browser window size to "450" x "850"

    ###########################################
    # Registration Validation with empty info #
    ###########################################

  @javascript
  Scenario: RegisterValidationWithEmptyInfo
    Given I am on "/compte/confirmation.html"
    Then I should see "Compte introuvable"
    # DEMO TEMPORIZATION
    Then Wait for demo

    #########################################
    # Registration Validation invalid Email #
    #########################################

  @javascript
  Scenario: RegisterValidationInvalidEmail
    Given I am on "/compte/confirmation.html?email=fake@frogg.fr&token=fakerandomtoken"
    Then I should see "Compte introuvable"
    # DEMO TEMPORIZATION
    Then Wait for demo


    ##############################################################
    # Registration Validation invalid Token with enabled account #
    ##############################################################

  @javascript
  Scenario: RegisterValidationInvalidToken
    Given I am on "/compte/confirmation.html?email=test@frogg.fr&token=fakerandomtoken"
    Then I should see "Votre compte est déjà activé"
    # DEMO TEMPORIZATION
    Then Wait for demo


    ###################################
    # Make A Recover Password Request #
    ###################################

  @javascript
  Scenario: RecoverRequest
    # going to recover page from login button from home
    Given I am on "/"
    And I should see "Gestionnaire de stock en ligne"
    And I follow "security_connexion_small"

    And I should see "Se connecter"
    And I follow "security_recover"
    And I should see "Récupération du mot de passe"
    When I fill in "user_recover_email" with "test@frogg.fr"
    And I press "Récuperer"

    Then I should see "Un mail a été envoyé à votre adresse dans lequel vous trouverez les informations pour récupération votre mot de passe."
    # DEMO TEMPORIZATION
    Then Wait for demo

    ##############################
    # Validate Recovery Password #
    ##############################

  @javascript
  Scenario: RecoverValidation
    # set windows size and reset
    Given I set browser window size to "450" x "850"
    # going to mail trap to test mail
    Given I am on "https://mailtrap.io/signin"
    Then I should see "Log In"
    When I fill in "user_email" with "mailtrap@frogg.fr"
    And I fill in "user_password" with "nimpmailtrap"
    And I press "commit"
    When I wait "5" sec
    Then I should see "My Inboxes"
    And I follow "Demo inbox"
    When I wait "5" sec
    And I click on selector "LI.email.new-email"
    When I wait "3" sec
    Then I should see "Votre requête de récupération de mot de passe"
    And I open an iframe on Selector "IFRAME.flex-item" from server "https://mailtrap.io"
    ## ==> Screenshot
    And I take a screenshot "password-recover-mail.png"
    When I wait "3" sec

    Then I should see "Récupération du mot de passe"
    And I follow "Récuperer"

    # IMPORTANT
    Then I switch tab
    When I wait "3" sec
     # test password page
    Then I should see "Changer de mot de passe"
    And I fill in "user_password_password" with "NewPassword"
    And I press "button_change_password"
    Then I should see "Votre mot de passe a été changé avec succès."

    # test connexion page
    When I wait "3" sec
    When I fill in "_username" with "test@frogg.fr"
    And I fill in "_password" with "NewPassword"
    And I press "button_security_connexion"

    Then I should see "Mon Stock"
    # DEMO TEMPORIZATION
    Then Wait for demo

    #############################################
    # Password Validation with bad inforamtions #
    #############################################

  @javascript
  Scenario: PasswordValidationWithEmptyInfo
    Given I am on "/compte/mot-de-passe/confirmation.html"
    Then I should see "Compte introuvable"
    # DEMO TEMPORIZATION
    Then Wait for demo

  @javascript
  Scenario: PasswordValidationInvalidEmail
    Given I am on "/compte/mot-de-passe/confirmation.html?email=fake@frogg.fr"
    Then I should see "Compte introuvable"
    # DEMO TEMPORIZATION
    Then Wait for demo

  @javascript
  Scenario: PasswordValidationEmptyToken
    Given I am on "/compte/mot-de-passe/confirmation.html?email=test@frogg.fr"
    Then I should see "Requête invalide"
    # DEMO TEMPORIZATION
    Then Wait for demo

  @javascript
  Scenario: PasswordValidationInvalidToken
    Given I am on "/compte/mot-de-passe/confirmation.html?email=test@frogg.fr&token=faketoken"
    Then I should see "Requête invalide"
    # DEMO TEMPORIZATION
    Then Wait for demo

  @javascript
  Scenario: PasswordValidationExpiredToken
    When I set "test@frogg.fr" token as expired with value "newtoken"
    And I am on "/compte/mot-de-passe/confirmation.html?email=test@frogg.fr&token=newtoken"
    Then I should see "Votre requête a expiré"
    # DEMO TEMPORIZATION
    Then Wait for demo

    #################
    # Clear Mailbox #
    #################

  @javascript
  Scenario: ClearMailBox
    # set windows size and reset
    Given I set browser window size to "450" x "850"
    # going to mail trap to test mail
    Given I am on "https://mailtrap.io/signin"
    And I should see "Log In"
    And I fill in "user_email" with "mailtrap@frogg.fr"
    And I fill in "user_password" with "nimpmailtrap"
    And I press "commit"
    When I wait "3" sec
    Then I should see "My Inboxes"
    And I follow "Demo inbox"
    When I wait "3" sec
    And I follow "Clear inbox"
    And I confirm
    # DEMO TEMPORIZATION
    Then Wait for demo

  # TODO
  # ADD : screenshot