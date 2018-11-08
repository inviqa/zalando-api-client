Feature: An article price update request is made against the Zalando REST API
    In order to update a Zalando article price
    As a developer
    I want article price data to be sent to Zalando via the API

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
