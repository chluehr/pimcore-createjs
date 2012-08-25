Pimcore - CreateJS Plugin
=========================

This Pimcore plugin enables a different way to edit content. It uses Create to do its magic, see: [Create.js](http://createjs.org/) 

For now, this is just an experimental "proof of concept"!


## Features

* Uses plain HTML RDFa-(a)nnotated templates to make pages automagically editable, sorta


## Installation / Setup

* Copy CreateJS from the plugins directory to the plugins directory of your Pimcore installation. 
* Open the Pimcore administrative backend as an admin user and activate and enable the CreateJS extension.
* Remove the default Pimcore editing frontend controller and replace it with the CreateJS implementation. This could be done globally or even on a action-by-action basis. See examples/DefaultController.php
* use RDFa based templates. See examples/default.php. Please note, that for the example to work you need to define a Pimcore object class named "participant" with the input fields "name" and "age" *AND* have an existing object instance with the id "5" (or change the template) to test this ...
* Namespaces _MUST_ be declared on the body tag

## Mode of operation: Edit Mode

* The CreateJS_Controller_Plugin injects the CreateJS javascript code into the page if a page is rendered in the Pimcore edit context.
* Then the CreateJS takes over, sending the edited info back via a backbone sync to the CreateJS_AdminController.
* Currently, the CreateJS_AdminController just decides if the content is a document or an object based on the subject attribute and stores the data.


## Mode of operation: Live view

* The CreateJS_Controller_Plugin parses the HTML code into a DOM and
* searches for various RDF annotation, then
* loads the data and injects the contents.


## Notes, Warnings, Todos -  _A LOT_

* only simple string fields are supported right now
* pimcore preview works only after save & publish
* save & publish is still needed after performing a CreateJS save!
* creating new objects does not work
* collections are not supported
* the backbone sync php backend needs to be rewritten from scratch, as it is based on simple/ugly string parsing ...
* no error checking is performed
* the code is extremely insecure
* externalize all required dependencies .. ?


