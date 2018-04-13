  Feature: My project is accessible
    # not technic action
    In order to access to my project
    # utilisateur
    As a visitor
    # technic action
    I need to see pages via my browser

    @javascript
    Scenario: Register
      Given I am on "/"
      When I wait "2" sec
      Then I should see "Gestionnaire de stock en ligne"
      And I follow "register-button"
      When I wait "2" sec
      Then I should see "Création de compte"
      When I fill in "user_firstname" with "FroggTest"
      And I fill in "user_lastname" with "FroggTest"
      And I fill in "user_password" with "FroggTest"
      And I fill in "user_email" with "test@frogg.fr"
      And I press "S'enregistrer"
      When I wait "2" sec
      Then I should see "Cette addresse de messagerie est déjà utilisé."

    #@javascript
    #Scenario: I try to register
    #  Given I am on "/"
    #  When I wait "2" sec
    #  Then I should see "Gestionnaire de stock en ligne"

    #register-button

    #Scenario: Before test i recreate empty database
    #  Given I start the scenario
      #  Then I recreate database

      #@javascript
   # Scenario: Add a contact with success
   #   Given I am on "/contact/create.html"
   #   When I fill in "contact_firstname" with "Frogg"
   #   And I fill in "contact_lastname" with "Frogg"
     #   And I fill in "contact_phone" with "0000000"
   #   And I fill in "contact_email" with "test@frogg.fr"
   #   And I press "Edit"
   #   And I wait "2" sec
   #   Then I should see "Saved !"
     #   And Contact "test@frogg.fr" should be in database