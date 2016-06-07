Feature: Use plugin
  In order to give others access to unpublished articles
  As an administrator
  I need to be able to create urls with a given validity
  
  Scenario: Don't redirect if no default redirect location and redirect mapping is set
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    When I go to "/"
    Then I should see "Hallo Welt!"

  Scenario: Don't redirect if empty default redirect location and redirect mapping is set
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value ""
    And the option "language_redirect_redirect_mapping" has the value ""
    When I go to "/"
    Then I should see "Hallo Welt!"

  Scenario: Redirect to default redirect location
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value "/license.txt"
    And the option "language_redirect_redirect_mapping" has the value ""
    When I go to "/"
    Then I should see "WordPress - Web publishing software"
