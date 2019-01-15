Feature: An authentication request is made against the Zalando REST API
    In order to communicate with the Zalando API
    As a developer
    I need to authenticate with the Zalando API

    Scenario: Authenticate and acquire a bearer token
        Given an authentication parameters file does not exist
        When I authenticate
        Then a Zalando API call to authenticate was made
        And the authentication parameters returned were authenticated most recently
        And the access token is "f5f439951cf41034240e4fc14dc75c03"
        And the access token expires in 7200 seconds
        And the access token has not expired

    @integration
    Scenario: Authenticate and acquire a bearer token
        Given an authentication parameters file does not exist
        When I authenticate
        Then a Zalando API call to authenticate was made
        And the authentication parameters returned were authenticated most recently
        And the access token has not expired

    Scenario: Authentication with the Zalando API fails
        Given an authentication parameters file does not exist
        And the Zalando API will fail to authenticate
        When I authenticate
        Then a Zalando API call to authenticate was made
        And authentication parameters were not returned
        And I received an error response containing the message "Invalid credentials"

    @integration
    Scenario: Authentication with the Zalando API fails
        Given an authentication parameters file does not exist
        And the Zalando API will fail to authenticate
        When I authenticate
        Then a Zalando API call to authenticate was made
        And authentication parameters were not returned
        And I received an error response containing the message '{"error":"unauthorized_client","error_description":"Invalid client secret"}'

    Scenario: Authentication uses a previously acquired Zalando API bearer token
        Given there is an authentication parameters file containing:
            | authenticated_at | 1 minute ago                     |
            | access_token     | d70e25e734dedbd1ca52fe81459b23af |
            | expires_in       | 600                              |
        When I authenticate
        Then a Zalando API call to authenticate was not made
        And the authentication parameters returned matches the previous authentication parameters

    Scenario: Authenticate and acquire a new bearer token if the previous authentication bearer token has expired
        Given there is an authentication parameters file containing:
            | authenticated_at | 10 minutes ago                   |
            | access_token     | d70e25e734dedbd1ca52fe81459b23af |
            | expires_in       | 600                              |
        When I authenticate
        Then a Zalando API call to authenticate was made
        And the authentication parameters returned does not match the previous authentication parameters
