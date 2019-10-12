# Address Model

## Overview

The `\app\models\Address` represent a geographical location in terms of street, city, province, country.

## Relation

An Address :
- belongs to 0 or *n* [Contacts](contact.md) : more than one person/company can live at a given address.