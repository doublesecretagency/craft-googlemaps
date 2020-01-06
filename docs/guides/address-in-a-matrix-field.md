# Address in a Matrix Field

## Basic Usage

Generally speaking, using an Address field contained within a Matrix field is fairly straightforward.

**Getting a normal address value:**

```twig
{% set address = entry.myAddressField %}
```

**Getting an address from a matrix block:**

```twig
{% for block in entry.myMatrixField.all() %}
    {% set matrixAddress = block.myMatrixAddressField %}
{% endfor %}
```

## Output a Map of Matrix Blocks

Since a Matrix Block is a normal Element Type, it works quite similarly to Entries. You can easily show a map of all blocks in your matrix, or just a single block. 

**Show all blocks:**

```twig
{% set blocks = entry.myMatrixField.all() %}
{{ googleMaps.dynamic(blocks) }}
```

**Show a single block:**

```twig
{% set block = entry.myMatrixField.one() %}
{{ googleMaps.dynamic(block) }}
```
