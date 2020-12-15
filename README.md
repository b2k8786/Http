# Http

### Example
#### GET Request
```php
Http::get('https://www.php.net/manual/en/function.curl-setopt.php',null,function($res){
    echo $res;
});
```
#### POST Request
```php
Http::post('https://www.php.net/manual/en/function.curl-setopt.php',["name"=>"abc","contact"=>98xxx988x8],function($res){
    echo $res;
});
```
