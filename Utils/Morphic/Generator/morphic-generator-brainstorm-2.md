Morphic generator brainstorm 2
===========================
2018-02-12




Structure & core concepts
====================
2018-02-12



Nomenclature
---------------

The generator will generate lists and forms from mysql tables, basically.
For now we are going to generate two types of elements, identified on their (mysql table) relationship:

- simple element,
        a single table representing an object.
        For instance, the ek_user table represents the user.
        
- context element
        an object depending from another.
        Usually, it corresponds to the has table in a has relationship.
        For instance, the ek_user_has_address defines what addresses an user owns, and how she owns them.
        So the table structure would look like this:
        
            - ek_user                   (simple element)
            - ek_user_has_address       (context element)
            - ek_address                (simple element)
        
        One thing about context element with this generator technique is that we keep the semantical relationship
        between tables, and so the synopsis is that we first edit an user, and from the user's edit page
        we click the "See user Paul's addresses" button, redirecting to the context element page.
        The context element page is bound to a specific user (in this case Paul): we don't have a context page
        without a source object called sometimes parent.
        
        

This is called the element type.         
As far as the generator goes, the generator should be able to guess the element type by examining the table structure.
However, just being precautious here, we will allow the user to infer the element type if she wants to (human are always
smarter than bots in complex cases), using the elementType key of an operation. 



### Ric split into parent/children keys

Therefore, with "context elements", the ric array can be split in two parts:

- parent keys, the keys representing the parent/source object (in the example above, this would be an array containing the user_id key)
- children keys, the other keys; so basically array diff between the ric and the parent keys (in the example above, this would be an array containing the address_id key)







File: the atomic structural unit
-------------------------------------

The generator executes operations.
Operations are organized in files.

The developer creates a file and put any number of operations she wants in it.
The developer can create as many file as she wants.

The generator is then told to execute all the operations in a given file.
The generator will execute all operations in a given file.

In other words, the file is the atomic structure element that the developer uses to 
organize her operations.


Inside a file, the $operations array defines the operations to execute.
Each entry of this array is an operation.

As for now, all operations have the same goal: create a morphic element.
However, it is possible that in the future other types of operations will exist,
hence we used the term "operation" instead of "morphicElementConfig".



### Operations

The following keys are recognized by the generator:


- ?operationType: create
        this is implicit, you don't need to set this value;
        it represents the type of operation for the generator to execute.
        As for now, the only possible value is create.
        All other values will be ignored.
- ?elementType: string
        the element type amongst:
        - simple
        - context
        - ...maybe more to come
        If not set, the bot will choose by itself.
        Note: it will use recognition of the "_has_" keyword (or alike) in the table name, because this word
        infers the direction that the bot needs.
        
- elementTable: string
        the table used for the morphic element 
- elementName: string 
        The table name without the prefix (if any), for instance: 
        - product_group (an example prefixed table would be ek_product_group)
        
        Some modules use the elementName to derive some other variables, like for instance
        the elementClass (xiao generator) in NullosAdmin module.
        So, be sure that the elementName is really representing the table.
        
        
        
- elementLabel: string
        the lower case singular version of the item's label
        Note to myself: this could be some php code as well (a code calling translation), sounds like subclassing...
        Example:: groupe de produits         
- elementLabelPlural: string 
        the lower case plural version of the item's label
        Note to myself: this could be some php code as well (a code calling translation), sounds like subclassing...
        Example:: groupes de produits
- elementRoute: string
        the route to use.
        For instance: NullosAdmin_Ekom_ProductGroup_List
- ric: string|array
        The ric for the table
        Note to myself: do only array...
- ?icon: string 
        // note to myself: this entry might only belong to nullos admin module
        The icon to use in the menu
        Example: "fa fa-list"
        Default: "fa fa-bomb"

// the following are added automatically by the generator, for the dev convenience
  
- ?columnLabels: array
    First, we try to find the label of a particular column in the tables entry (more specific).
    If no result is yield, then we try in the default entry.
    If there is still no result, we simply return a guess based on the $columnName: 
    we replace underscores by spaces, and return the ucfirst version of that.
    
    - tables: array 
        $tableName: array of $columnName => $label    
    - default: array of $columnName => $label    
        
        
        
- columns: array of columns of the table
- columnTypes: array
    It's used to find the control type that the generator should generate.
    The possible types that the generator understands are the following:
        - input
        - textarea
        - date
        - datetime
        - ...your custom fields
    
    First, we try to find the type for a particular column in the tables entry (more specific).
    If no result is yield, then we try in the default entry.
    If there is still no result, we simply return a guess based on the mysql column type.
    - tables: array 
        $tableName: array of $columnName => $type    
    - default: array of $columnName => $type
    
    Note, $type can be a string, or even an array if appropriate (I'm thinking about controls like upload,
    which might need more parameters like the srcDir, the transformation to apply, ...).
    
    The default guessing is the following:
    - sqlType=text => textarea
    - sqlType=date => date
    - sqlType=datetime => datetime
    If all fails, default is input
    
- columnFkeys: array (see QuickPdoInfoTool::getForeignKeysInfo for more details)
    - columnName:
        - db            
        - table            
        - field            
- ai: string|false, auto-incremented field

- ?formInsertSuccessMsg: 
    the notification message to display when the form in insert mode was successfully posted
    If not set, a default message will be compiled.
    The default message looks like this: "The item $elementLabel has been added".

- ?formUpdateSuccessMsg: 
    the notification message to display when the form in update mode was successfully posted
    If not set, a default message will be compiled.
    The default message looks like this: "The item $elementLabel has been updated".

- ?pivot: array with the following structure:
    - ?mode: discover|manual, default=same as the default defined at the configuration level
            
            Defines how the generator behaves in regard to pivotLinks.
            In particular, it populates the formAfterElements entry of a form config.
            
            If "discover" is set, then the generator tries to find context tables by itself.
            The discovered tables act as if they were added manually in the "tables" entry.
            
            If "manual" is set, then the generator skips the discovering phase.
            
    - ?removeTables: array of $table
            it might be the case that the bot finds some tables that you don't want to use as context tables.
            In that case, you (the developer) can dismiss the bot's discovered tables by adding them to this array.
    - ?tables: array of $table => $tableInfo
            it might be the case that you want to add some context tables that the bot didnt' found.
            When this is the case, add them in this array.
            
            Note: if a table is found both in the tables and removeTables entries, then the removeTables table
            has precedence. 
            
            $tableInfo:
            - ?route: the route of the pivot link
            - ?text: the text to display on the pivot link
            
            Note that all properties of tableInfo are optional.
            This means that you can left this array empty, and the bot will guess the properties for you.






### Configuration        
        
We can also configure the generator using the configuration array ($configuration), which contains the following entries:        
        
- ?elementName2Label: array
        map to override default guessing of the generator.
        The default array is empty.
        The default conversion routine will convert "product_group" into "product group".        

  
- ?autoCompletes: array
        map to override default guessing of the generator.
        The default array is empty.
        The default conversion routine will convert "product_group" into "product group".        

- ?pivotMode: discover|manual, default=discover
        Defines how the generator behaves in regard to pivotLinks.
        See pivot.defaultMode (at the operation level) for more details.
         

- ?dbPrefixes: array of db prefixes
        The bot can do some extra work if you specify the db prefixes


The conservative policy: Generator never overrides an existing file
===============================================
2018-02-12




The generator never overwrite an existing file.
That's the rule of thumb of the generator.

Example from my own experience with the EkomNullosMorphicGenerator, 
which is the concrete version of the MorphicGenerator used by the Ekom module.
This generator spits out the code for the developer to paste into her code.

When more can be done, the generator will do more.

For instance, when generating the controller code, 
the generator will try to see if the controller file already exist.
If this is the case, then nothing is done.
But if the file doesn't exist yet, it will be created.

Using this technique, we prevent some dramatic mistakes to occur.





Dictionary
==============
2018-02-13


The dictionary holds the label in singular form (key=0) and plural form (key=1)
for the tables that represent an object (not the has tables, which bind
objects together but aren't object themselves).

The dictionary is used by the generator, for instance when creating 
pivot links.

To help with creating a dictionary from scratch, we can use the  

MorphicGeneratorHelper::displayEnglishDictionaryCode method, or we could 
create the array manually. 



