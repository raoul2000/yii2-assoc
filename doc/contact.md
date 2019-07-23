# Contact Model

## Overview

The `app\models\Contact`model represents 2 kinds of entities :
- a physical person (like "Bob Marley", "Gandhi", "Alice Cooper",etc...)
- a non physical person (like "Microsoft", "the French Soccer Team", "The Pink Floyd", etc...)

## Relations

A Contact can be linked to several other models :

- a contact can have O or 1 address
- a contact can have O or n bank account(s)
- a contact can have O or n category
- a contact can be a provider for O or n order(s)
- a contact can be a consumer for O or n order(s)
- a contact can be related to O or n contact(s)
