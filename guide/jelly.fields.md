# Jelly Fields

Each property of the model is represented by a field object. Each field object
has, at the very least, three methods for setting (`set()`), getting
(`get()`), and saving (`save()`) values that the model proxies to when
necessary. In general, you will never need to touch these methods, as Jelly
handles that for you. Keep in mind that Fields never manage state, as they are
shared by all instances of a model.

------------------------------------------------------------------------------

#### Basic Properties

[column](#field-prop-column) | [primary](#field-prop-primary) |
[in_db](#field-prop-in_db) | [default](#field-prop-default) |
[unique](#field-prop-unique) | [editable](#field-prop-editable) |
[filters](#field-prop-filters) | [rules](#field-prop-rules) |
[callbacks](#field-prop-callbacks) | [label](#field-prop-label) |
[description](#field-prop-description)

#### Basic Field Types

[Primary](#field-primary) | [String](#field-string) |
[Integer](#field-integer) | [Float](#field-float) | [Boolean](#field-booleam)
| [Enum](#field-enum) | [Timestamp](#field-timestamp) | [Slug](#field-slug) |
[Text](#field-text) | [HTML](#field-html) | [Email](#field-email) |
[Password](#field-password)

#### Relationship Field Types

[Belongs To](#field-belongsto) | [Has Many](#field-hasmany) | 
[Has One](#field-hasone) | [Many to Many](#field-manytomany)

#### Field Behaviors

[Saveable](#jelly-saveable) | [Joinable](#jelly-joinable) |
[Haveable](#jelly-haveable) | [Changeable](#jelly-changeable)

------------------------------------------------------------------------------

## Properties

Each field has a set of default properties that have various uses. If you pass
a string to the field's constructor, it will be used as the actual column's
name in the database. Otherwise, you can pass an array to override multiple
properties:

    $meta->fields += array(
    	'id' => new Jelly_Field_Primary('actual-column-name'),
    	'name' => new Jelly_Field_String(array(
    		'column' => 'actual-column-name',
                    'default' => 'some default',
    		'rules' => array(
    			'not_empty' => TRUE
    		)
    	)),
    );


<h4 id="field-prop-column">column (string)</h4>

**Default** The name of the field
The name of the column as it exists in the database.

[!!] **Note**: This column has different uses (and sometimes no use at all) for relationships.

------------------------------------------------------------------------------

<h4 id="field-prop-primary">primary (boolean)</h4>

**Default** FALSE except for `Field_Primary`, which defaults to TRUE
Whether or not the column is a primary key. 

The first primary key field found will be used for single-row database
interactions and relationships.

------------------------------------------------------------------------------

<h4 id="field-prop-in_db">in_db (boolean)</h4>

**Default** TRUE except for `Field_HasOne`, `Field_HasMany` and `Field_ManyToMany`, which default to FALSE

Whether or not the column actually exists in the model's table.

------------------------------------------------------------------------------

<h4 id="field-prop-default">default (mixed)</h4>

**Default** NULL

The default value. Every model, upon instantiation, is populated with this
data from each field. It will also be inserted when `save()`d, if no other
data is set.

------------------------------------------------------------------------------

<h4 id="field-prop-unique">unique (boolean)</h4>

**Default** FALSE

If set to TRUE a validation callback will be set that ensures the value for
this field is unique in the table.

------------------------------------------------------------------------------

<h4 id="field-prop-editable">editable (boolean)</h4>

**Default** FALSE except for `Field_Primary` and `Field_Serialized`

Specifies whether or not the field can output a view. If you call `input()` on
a field that has this set to FALSE, `input()` will return FALSE instead of a
`View`.

------------------------------------------------------------------------------

<h4 id="field-prop-filters">filters (array)</h4>

**Default** NULL

An array of filters to use for Validation.

------------------------------------------------------------------------------

<h4 id="field-prop-rules">rules (array)</h4>

**Default** NULL

An array of rules to use for Validation. Some Fields may have a different
default. For example, `Field_Email` sets an `email` rule, by default.

------------------------------------------------------------------------------

<h4 id="field-prop-callbacks">callbacks (array)</h4>

**Default** NULL

An array of callbacks to use for Validation.

------------------------------------------------------------------------------

<h4 id="field-prop-label">label (string)</h4>

**Default** The name of the field run through `inflector::humanize`

A pretty, human-readable name for the column.

------------------------------------------------------------------------------

<h4 id="field-prop-description">description (string)</h4>

**Default** An empty string

A description or comment for the column.

------------------------------------------------------------------------------

## Basic Field Types

There are several Field classes to use for representing data-types. Some may
define their own special properties that you should be aware of.

------------------------------------------------------------------------------

<h4 id="field-primary">Field_Primary</h4>

Represents a primary key field. This can be either an integer or a string.
Support for composite keys is planned.

------------------------------------------------------------------------------

<h4 id="field-string">Field_String</h4>

Represents a string of any length.

------------------------------------------------------------------------------

<h4 id="field-integer">Field_Integer</h4>

Represents an integer.

------------------------------------------------------------------------------

<h4 id="field-float">Field_Float</h4>

Represents a float. 

* `places` *(int)* This property can be set to automatically round the float
  to the specified number of places.

------------------------------------------------------------------------------

<h4 id="field-boolean">Field_Boolean</h4>

Represents a boolean value.

* `true` *(mixed)* The value for TRUE as represented in the database.
* `false` *(mixed)* The value for FALSE as represented in the database.
* `pretty_true` *(string)* A pretty way of saying TRUE in the input view (Defaults to "Yes").
* `pretty_false` *(string)* A pretty way of saying FALSE in the input view (Defaults to "No").

------------------------------------------------------------------------------

<h4 id="field-enum">Field_Enum</h4>

Represents an enumerated list. If an attempt is made to set it to a value that
is not in the `choices` array, it is set to the default.

* `choices` *(array)* An array of valid choices

------------------------------------------------------------------------------

<h4 id="field-timestamp">Field_Timestamp</h4>

Represents a timestamp. Internally, this comes back as a UNIX timestamp. 
If you set the format property, the value will be converted before its
thrown back into the database. This means this field can represent many
different ways of storing dates in the database.

* `format` *(string)* A valid
  [date](http://us2.php.net/manual/en/function.date.php) string to use to
  convert the time to the database's format upon update or insert
* `auto_now_create` *(boolean)* Whether or not to automatically set the field
  to `now()` on creation (Defaults to FALSE)
* `auto_now_update` *(boolean)* Whether or not to automatically set the field
   to `now()` on update (Defaults to FALSE)
* `pretty_format` *(string)* A valid
  [date](http://us2.php.net/manual/en/function.date.php) string to use to
  convert the time to a pretty format when displaying in an input

------------------------------------------------------------------------------

<h4 id="field-slug">Field_Slug</h4>

Represents a URL slug, such as those set for blog posts. Whenever this is
`set()`, the value is stripped so that it only contains lowercase letters,
dashes, backslashes, and numbers. Any underscores or other characters are
converted to dashes and the result is run through `preg_replace` to ensure
there aren't multiple dashes in a row.

------------------------------------------------------------------------------

<h4 id="field-text">Field_Text</h4>

Represents a block of text.

------------------------------------------------------------------------------

<h4 id="field-html">Field_HTML</h4>

Represents a block of HTML.

------------------------------------------------------------------------------

<h4 id="field-email">Field_Email</h4>

Represents a valid email address. This automatically sets the `rules` property
to validate it as an email.

------------------------------------------------------------------------------

<h4 id="field-password">Field_Password</h4>

Represents a password.

* `hash_with` *(callback)* A valid callback to hash the password when it is set

------------------------------------------------------------------------------

## Relationship Fields

These fields, with the exception of `Field_BelongsTo` all default to having
`$in_db` set to FALSE. Any field with `$in_db` set to FALSE is not expected to
return a value for saving like normal fields, instead, if they implement
`Jelly_Behavior_Field_Saveable` they will be expected to handle their own
saving.

------------------------------------------------------------------------------

<h4 id="field-belongsto">Field_BelongsTo</h4>

Represents a **belongs_to** relationship. 

 * `column` *(string)* The name of the column in the table that references the
   foreign model's primary key. Defaults to the field name plus '_id'.
 * `foreign` *(array)* The foreign model and column this field references
 
This is expected to contain an assoc. array containing the key 'model', and the key 'column'

If they do not exist, they will be filled in with sensible defaults derived
from the field's name. If 'model' is empty it is set to the singularized name
of the field. If 'column' is empty, it is set to 'id'.

    ...
    'category' => new Field_BelongsTo(array(
        'column' => 'category_id',
        'foreign' => array(
            'model' => 'category', // This is what it would be by default
            'column' => 'id' // This is also the default
        ),
    ));
    ...

[!!] **Implemented Field Behaviors**: Jelly_Behavior_Field_Joinable

------------------------------------------------------------------------------

<h4 id="field-hasmany">Field_HasMany</h4>

Represents a **has_many** relationship. 

 * `foreign` *(array)* The foreign model and column this field references

This is expected to contain an assoc. array containing the key 'model', and
the key 'column'

If they do not exist, they will be filled in with sensible defaults derived
from the field's name. If 'model' is empty it is set to the singularized name
of the field. If 'column' is empty, it is set to the name of the model plus
'_id'

    ...
    'posts' => new Field_HasMany(array(
        'foreign' => array(
            'model' => 'post', // This is what it would be by default
            'column' => 'category_id' // This is also the default
        ),
    ));
    ...


[!!] **Implemented Field Behaviors**: Jelly_Behavior_Field_Saveable, Jelly_Behavior_Field_Haveable, Jelly_Behavior_Field_Changeable

------------------------------------------------------------------------------

<h4 id="field-hasone">Field_HasOne</h4>

Represents a **has_one** relationship. This takes the exact same options as
the above `Field_HasMany`, the only difference is that the field only allows
one reference.

[!!] **Implemented Field Behaviors**: All behaviors that `Field_Has_Many` implements, plus Jelly_Behavior_Field_Joinable

------------------------------------------------------------------------------

<h4 id="field-manytomany">Field_ManyToMany</h4>

Represents a **many-to-many** relationship. 

 * `foreign` *(array)* The foreign model and column this field references

This is expected to contain an assoc. array containing the key 'model' and the
key 'column'

If they do not exist, they will be filled in with sensible defaults derived
from the field's name. If 'model' is empty it is set to the singularized name
of the field. If 'column' is empty, it is set to 'id'

 * `through` *(array)* The through model and column this field goes through to connect the two models

If 'model' is empty it is set to the pluralized names of the two model's names
combined alphabetically with an underscore.

'columns' is an array of two columns, the first item is the column that joins
the model the field is related to, the second item is the column that relates
to the foreign model.

[!!] **Note:** While it is stressed elsewhere, it is important to note that 'model' and column, in this case do not actually need to represent a model and that model's field. While it is acceptable to do so, it's also acceptable for those items to directly represent tables and columns in the database.

    // Assume we are joining posts to categories and this declaration is for the post model
    ...
    'categories' => new Field_HasMany(array(
        'foreign' => array(
            'model' => 'category', // This is what it would be by default
            'column' => 'id' // This is also the default
        ),
        'through' => array(
            'model' => 'categories_posts', // This is what it would be by default
            'columns' => array('post_id', 'category_id') // This is also the default
        ),
    ));
    ...

[!!] **Implemented Field Behaviors**: Jelly_Behavior_Field_Saveable, Jelly_Behavior_Field_Haveable, Jelly_Behavior_Field_Changeable

------------------------------------------------------------------------------

## Field Behaviors

Some field types (mainly those concerned with relationships) may implement
certain interfaces that let the model know that they do extra special things
other than simply converting and processing data. The following is a list of
what interfaces are currently recognized by Jelly and the methods these
interfaces require the fields to implement.

------------------------------------------------------------------------------

<h4 id="jelly-saveable">Jelly_Behavior_Field_Saveable</h4>

 * *public function* **save**($model, $value);

Any field that is `!in_db` and implements this declares that it handles its
own database interaction when `save()` is called in the model. If the data for
this field has changed, it will be passed to the field's `save()` method. Most
relationships implement this so that they can update other tables when the
relationship has changed.

[!!] **Note**: in fact, all field types implement save() with the same method signature, the difference is that this is called *after* the model has finished saving `in_db` columns. Thus, `$model` is loaded and fresh, and you have access to a real value for the model's primary key.

------------------------------------------------------------------------------

<h4 id="jelly-joinable">Jelly_Behavior_Field_Joinable</h4>

* *public function* **with**($model, $relation, $target\_path, $parent\_path);

Declares a field that can be joined using Jelly_Model's with() method. The
with() method that the field implements is expected to complete the join()
clause on `$model`. Since objects are passed as references, nothing needs to
be returned.

Using $target\_path and $parent\_path properly is very important. All join and
on clauses must use the aliases provided by $target\_path and $parent\_path so
that the query can properly differentiate between joins. This is only
necessary for certain edge cases, but it is still necessary for a complete,
bug-free implementation.

For examples, check out Field\_BelongsTo and Field\_HasOne's implementations.

------------------------------------------------------------------------------

<h4 id="jelly-haveable">Jelly_Behavior_Field_Haveable</h4>

 * *public function* **has**($model, $ids);

Declares a field that can return whether or not it *has* a relationship to
another model or models.

The `has()` should return a boolean that indicates whether or not the `$model`
passed is related to the $ids passed.

An array of `$ids` is always passed. For fields that are a 1:1 mapping, how
they deal having more than 1 primary key passed is undefined, though it is
recommended to simply use the first id in the array.

To return TRUE, the model must have ALL `$ids` (except for the above edge
case).

The `$model` should not be modified by the time this method has finished. If
you need to query something on `$model` specifically, construct a new one.

------------------------------------------------------------------------------

<h4 id="jelly-changeable">Jelly_Behavior_Field_Changeable</h4>

Declares a field that can have individual records add()ed or remove()ed from
it.

This interface does not require any methods to be declared, but rather acts as
a flag for the model so that it knows the field supports this feature.

In general, relationships that are not 1:1 but are 1:many or many:many should
implement this interface. This means that in the field's `set()` method, it
should expect to handle a multitude of values including:

 * A primary key
 * Another Jelly model
 * An iterable collection of primary keys or Jelly models, such as an array or Database_Result

Additionally, in the field's `save()` method, it should expect an array of
primary keys for the `$value`.

## Methods

Just for reference's sake, here is a brief overview of the purpose of each
method, should the need arise to work with the field directly.

**get()**:

Returns a value that is nicer to work with in your PHP. Most fields have
already been converted to a nicer value in `set()` and simply return the value
passed to them, however some—such as relation fields—will return another Jelly
object that you can interact with.

**set()**:

This method also handles conversion of data, however it is converted when data
comes in, unlike `get()`, which converts as it goes out. Generally, simpler
conversions are handled here, such as converting strings to proper integers,
dates to UNIX timestamps, and so on.

**save()**:

This method returns a value suitable for saving in the database. If the field
is not part of the actual table that the model represents, it may also handle
saving data to other tables.