Feature: An article price update request is made against the Zalando REST API
    In order to update a Zalando article price
    As a developer
    I want article price data to be sent to Zalando via the API

    Scenario: Create an article price update request
        When I create an article price update request with the following details
            | regular price      | 34.99                                |
            | currency           | EUR                                  |
            | VAT code           | 2                                    |
            | merchant simple ID | cool-blue-shoes                      |
            | ean                | 9780679762881                        |
            | sales channel ID   | bf48ba35-149d-4b76-8ac9-d08d126b517f |
            | fulfilled by       | Zalando                              |
        Then the article price update request content should be
            """
            {"price":{"regular_price":34.99,"currency":"EUR","vat_code":"2"},"merchant_simple_id":"cool-blue-shoes","ean":"9780679762881","sales_channel_id":"bf48ba35-149d-4b76-8ac9-d08d126b517f","fulfillment_type":"fulfilled_by_zalando"}
            """
