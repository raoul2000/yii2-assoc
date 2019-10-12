# Contact Model

## Overview

The `app\models\Contact`model represents 2 kinds of entities :
- a physical person (like "Bob Marley", "Gandhi", "Alice Cooper",etc...)
- a non physical person (like "Microsoft", "the French Soccer Team", "The Pink Floyd", etc...)

## Relations

A Contact can be linked to several other models :

- a contact can have O or 1 [address](address.md) : a contact lives at a signle given address
- a contact can have O or n [bank account(s)](bank-account.md) : for instance a "current account", a "savings account", etc.
- a contact can have O or n [category](category.md)
- a contact can be a provider for O or n [order(s)](order.md)
- a contact can be a consumer for O or n [order(s)](order.md)
- a contact can be related to O or n contact(s) : "bob" is related to "bill" through the relation "is friend of"
