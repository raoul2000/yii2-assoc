# Transaction Model

## Overview

The `app\models\Transaction` represents a value transfer between two [Bank Accounts](bank-account.md). The value is removed from the **emitter** account and added to the **recipient** account.

> terms **source** and **target** are also used to name the two bank accounts involved in a transaction

## Relations

A transaction : 
- has exactly 1 emmitter and 2 recipient account
- belongs to 0 or 1 [Transaction Pack](transaction-pack.md)
- can have 0 or *n* tags


Note that a transaction is not directly linked to a [Contact](contact.md) but through the [Bank Account](bank-acount.md) owned by a Contact.


