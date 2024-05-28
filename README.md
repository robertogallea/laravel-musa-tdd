## System description

The system allows to make **currency conversion requests**. For each request, the users specifies **the source currency,
the target currency and the amount they** want to convert.

The system must **validate data for invalid values**, such as
malformed currency code, negative amounts.

Whenever a request for a not existing currency is done, the system must
**return an error messsage** and **the administrator must be notified via email**.

**Any request done in the morning (A.M. hours) must be rejected**.


It should be possible to **use database** as conversion source. The
conversion source **could be optionally cached**.


## Required tests

- (X) it can convert amounts between arbitrary currencies
- (X) it validates currency conversion requests data
- (X) if a conversion for a not existing currency is requested, an error message with 404 status is returned
- (X) if a conversion for a not existing currency is requested, an email is sent to the admin
- (X) it blocks requests in A.M. hours
- (X) it allows requests in P.M. hours
- (X) it can convert between two currencies using db
- (X) it can convert between two currencies using cache

