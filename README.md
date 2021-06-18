# Http

### Example
#### GET Request
```php
(new Http)
    ->setHeader('Accept', 'json')
    ->parameter('q', 'value')
    ->get('xyz.com', function ($res) {
       print_r($res);
    });
```
#### POST Request
```php
(new Http)
    ->setHeader('Accept', 'json')
    ->parameter('q', 'value')
    ->post('xyz.com', function ($res) {
       print_r($res);
    });
```
