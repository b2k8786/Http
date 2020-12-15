# Http

### Example
#### GET Request
```php
Http::get('www.xyz.com',null,function($res){
    echo $res;
});
```
#### POST Request
```php
Http::post('www.xyz.com',["name"=>"abc","contact"=>98xxx988x8],function($res){
    echo $res;
});
```
