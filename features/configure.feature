Feature: Configure the plugin
  In order to use the plugin properly
  As an administrator
  I need to configure it

  Scenario: Preset default redirect location and redirect mapping
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value "/en/"
    And the option "language_redirect_redirect_mapping" has the value:
      """
      en=/en/
      de=/de/
      """
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=language-redirect.php"
    Then the "language_redirect_default_redirect_location" field should contain "/en/"
    And the "language_redirect_redirect_mapping" field should contain:
      """
      en=/en/
      de=/de/
      """

  Scenario: Set default redirect location and redirect mapping
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=language-redirect.php"
    And I fill in "language_redirect_default_redirect_location" with "/en/"
    And I fill in "language_redirect_redirect_mapping" with:
      """
      en=/en/
      de=/de/
      """
    And I press "submit"
    Then I should see the message "Settings saved"
    And the option "language_redirect_default_redirect_location" should have the value "/en/"
    And the option "language_redirect_redirect_mapping" should have the value:
      """
      en=/en/
      de=/de/
      """

  Scenario: Set empty values
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=language-redirect.php"
    And I press "submit"
    Then I should see the message "Settings saved"
    And the option "language_redirect_default_redirect_location" should have the value ""
    And the option "language_redirect_redirect_mapping" should have the value ""
