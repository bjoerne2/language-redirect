Feature: Use plugin
  In order to give others access to unpublished articles
  As an administrator
  I need to be able to create urls with a given validity

  # tests based on HTTP_ACCEPT_LANGUAGE de,en-US;q=0.7,en;q=0.3  

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

  Scenario: Redirect based on redirect mapping and first accepted language
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value ""
    And the option "language_redirect_redirect_mapping" has the value:
      """
      de=/license.txt
      en=/unknown.txt
      """
    When I go to "/"
    Then I should see "WordPress - Web publishing software"

  Scenario: Redirect based on redirect mapping and second accepted language
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value ""
    And the option "language_redirect_redirect_mapping" has the value:
      """
      en-us=/license.txt
      """
    When I go to "/"
    Then I should see "WordPress - Web publishing software"

  Scenario: Redirect based on redirect mapping and third accepted language
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value ""
    And the option "language_redirect_redirect_mapping" has the value:
      """
      en=/license.txt
      """
    When I go to "/"
    Then I should see "WordPress - Web publishing software"

  Scenario: Redirect to default redirect location when mapping doesn't match
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value "/license.txt"
    And the option "language_redirect_redirect_mapping" has the value:
      """
      fr=/unknown.txt
      """
    When I go to "/"
    Then I should see "WordPress - Web publishing software"

  Scenario: Do not redirect when mapping doesn't match and default redirect location is not set
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value ""
    And the option "language_redirect_redirect_mapping" has the value:
      """
      fr=/unknown.txt
      """
    When I go to "/"
    Then I should see "Hallo Welt!"
