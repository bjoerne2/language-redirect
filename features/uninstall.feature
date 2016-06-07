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
    And I deactivate the plugin "language-redirect"
    And I uninstall the plugin "language-redirect"
    Then I should see the message "The selected plugin has been deleted."
    And the option "language_redirect_default_redirect_location" should have the value "/en/"
    # current behavior, should not exist
