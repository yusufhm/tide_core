@tide
Feature: Access permissions

  Ensure that configuration permissions are set correctly for designated roles.

  @api
  Scenario Outline: Users have access to administrate menus
    Given I am logged in as a user with the "<role>" role
    When I go to "<path>"
    Then I should get a "<response>" HTTP response
    Examples:
      | role               | path                  | response |
      # Blocks.
      | authenticated user | admin/structure/block | 404      |
      | administrator      | admin/structure/block | 200      |
      | editor             | admin/structure/block | 200      |
      | approver           | admin/structure/block | 200      |
      | previewer          | admin/structure/block | 404      |
      # Menu.
      | authenticated user | admin/structure/menu | 404      |
      | administrator      | admin/structure/menu | 200      |
      | editor             | admin/structure/menu | 200      |
      | approver           | admin/structure/menu | 200      |
      | previewer          | admin/structure/menu | 404      |
