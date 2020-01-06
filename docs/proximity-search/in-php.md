# Proximity Search in PHP

Here is a general example...

```php
$entries = Entry::find()
    ->myAddressField([
        'target' => 'Los Angeles',
        'range' => 50
    ])
    ->orderBy('distance')
    ->all();
```

## `->myAddressField($params = [])`

## `->orderBy('distance')`


