Morphic generator brainstorm 2
===========================
2018-02-12




Structure & core concepts
====================
2018-02-12



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
- elementTable: string
        the table used for the morphic element 
- elementName: string 
        basically, the table name without the prefix, for instance: 
        - product_group (an example prefixed table would be ek_product_group)
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
        
        
        
- ?columnTypes: array
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
    
- ?columnFkeys: array (see QuickPdoInfoTool::getForeignKeysInfo for more details)
    - columnName:
        - db            
        - table            
        - field            

### Configuration        
        
We can also configure the generator using the configuration array ($configuration), which contains the following entries:        
        
- ?elementName2Label: array
        map to override default guessing of the generator.
        The default array is empty.
        The default conversion routine will convert "product_group" into "product group".        

  
- ?autoCompletes: array o
        map to override default guessing of the generator.
        The default array is empty.
        The default conversion routine will convert "product_group" into "product group".        




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








