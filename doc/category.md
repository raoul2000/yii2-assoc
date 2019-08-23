# Category

## Access

Categories are available to all contact 

**DEPRECATED**

Categories can be related to a single Contact or not related to any Contact. 
- Public Categories : not related to a contact
- Private Categories : related to a contact

Private Categories are accessible to the current user only if it impersonate a Contact. On the other hand, Public Categories are available when the user is not impersonating a Contact

## Subject

The *Subject* of a category is the model that can be assigned these categories. For a given Category, the subject is implemented by the `type` column.

Currently the following subjects are available : 
- Transaction (see next chapter)
- product

## The Transaction's categories Case

In a transaction, two actors are involved : the sender and the recipient (the source and the destination contact). In this context, and as there is no currently a distinction between these two roles, the one and only category that can be associated to a transaction may be confusing. This is because if the category reflects the role of the contact who assigned it, it may be meaningless for the counter part.

For example let's imagine a transaction where Bob send 10 euros to Bill to buy a TShirt. From Bob point of view (the buyer) the category of this transaction could be "clothe" or "fashion". For Bill (the seller) these categories make no sense and he would probabbly better assign the category "sell".

One solution to this situation would be to have 2 categories for each transaction : one set by the sender, and the other one set by the recipient.

This has not been implemented right now and deserver further analysis. For now it is appropriate to choose a generic category name for transactions.

