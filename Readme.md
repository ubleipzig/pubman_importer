# pubman_importer

This typo3 extension retrieves journals from an escidoc repository by querying it with its cql query language

## Dependencies

* [Typo3][1] > 6
* [t3jquery][2]
* [rx_shariff][3]

## Configuration

After adding the plugin to the page one can configure it according to one's needs:

* Escidoc Server URL
* Escidoc Server Path
* Escidoc Content Model Object Id
* Escidoc Context Object Id

To use the plugin's css and javascript one has to add the plugin-typoscript to the page's template:
Go to *List* and add a new *System Records:Template*. Under Tab *Includes* choose **Pubman Importer**

## Usage

Add the *Journals* plugin in your page and you will have your journals listed. You can descend into the issue and articles and even download the content of an issue or article if added.
If you have html content added then this content is integrated into the page. Make sure that it does not violate html conformity.

[1]: https://typo3.org/
[2]: https://typo3.org/extensions/repository/view/t3jquery
[3]: https://typo3.org/extensions/repository/view/rx_shariff