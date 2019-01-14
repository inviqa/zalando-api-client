Feature: An article price update request is made against the Zalando REST API
    In order to update a Zalando article price
    As a developer
    I want article price data to be sent to Zalando via the API

    Scenario: Authenticate and acquire a bearer token
        When I authenticate
        Then I will receive a new bearer token

#    Scenario: Authentication with the Zalando API fails
#        When I authenticate with the Zalando API that fails
#        Then I will receive an error response
#
#    Scenario: Authentication uses a previously acquired Zalando API bearer token
#        When I authenticate a Zalando API call will not be made
#        Then a previously acquired Zalando API bearer token will be used
#
#    Scenario: Authenticate and acquire a new bearer token if the previous authentication bearer token has expired
#        Given the authentication bearer token has expired
#        When I authenticate with the Zalando API
#        Then I will receive a new bearer token

    Scenario: Updating a Zalando article price
        When I update the article price with the following details
            | regular price      | 34.99                                |
            | merchant simple ID | cool-blue-shoes                      |
            | ean                | 9780679762881                        |
            | sales channel ID   | bf48ba35-149d-4b76-8ac9-d08d126b517f |
        Then a file with the following content will be written
            """
            {"price":{"regular_price":34.99,"currency":"EUR","vat_code":"2"},"merchant_simple_id":"cool-blue-shoes","ean":"9780679762881","sales_channel_id":"bf48ba35-149d-4b76-8ac9-d08d126b517f","fulfillment_type":"fulfilled_by_zalando"}
            """
