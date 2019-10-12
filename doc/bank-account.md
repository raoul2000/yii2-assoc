# Bank Account Model

## Overview

The `\app\models\BankAccount` model represent a bank account. It has a name and a value which can be positive (credit) or negative (debt). 

## Relations

A Bank Account:
- belongs to exactly one [Contact](contact.md)
- has 0 or *n* [Transactions](transaction.md) : For each transaction related to a bank account, the bank account can have the **emitter** or **recipient** role