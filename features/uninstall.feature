Feature: Uninstall plugin
  In order to clean up
  As an administrator
  I need to be able to uninstall the plugin without a footprint

  Scenario: Uninstall plugin
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And the plugin "language-redirect" is activated
    And the option "language_redirect_default_redirect_location" has the value "/en/"
    And I am logged as an administrator
    When I go to "/wp-admin/plugins.php"
    And I deactivate the plugin "Language Redirect"
    And I uninstall the plugin "Language Redirect"
    Then I should see the message "Language Redirect was successfully deleted."
    And the option "language_redirect_default_redirect_location" should not exist
    And the option "language_redirect_redirect_mapping" should not exist
