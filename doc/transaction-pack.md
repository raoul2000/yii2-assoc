# Transaction Pack Model

## Overview

The `\app\models\TransactionPack` model represent a named group of [Transaction](transaction.md) belonging to a given [Bank Account](bank-account.md)

## Relation

A Transaction Pack :

- contains 0 to *n* Transaction
- belongs to exactly one Bank Account

