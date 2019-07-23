# Contact Relation Model

The `app\models\ContactRelation` model represents a relation between 2 Contacts. A relation is an *oriented link* between two contact models and for this reason, a relation as a source contact and a target contact. A relation laso has a *type* atribute that describes it.

For example :
- real life : Bob is the father of Bill
- system : a relation with type 'childOf' links the source contact Bob to the target contact Bill

