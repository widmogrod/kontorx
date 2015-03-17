# Wprowadzenie do DataGrid #

FIXIT: poprawić stylistykę..

KontorX\_DataGrid -jest to biblioteka napisana w dla integracji z Zend Framework.
Zawiera już adaptery dla danych Zend\_Db\_Table, Zend\_Db\_Select, Array.
Prosta możliwość pisania własnych adapterów stanowi łatwy sposób na rozszerzenie funkcjonalność o inne biblioteki przechowywujące / pobierające struktury danych.

## Cel i zadaniem biblioteki ##

FIXIT: sformułować to lepiej

Cel i zadania biblioteki KontorX\_DataGrid jest:

  * wizualizacja tabelarycznych struktór danych.
  * filtrowania danych po różnych kryteriach
  * edytowania wierszy rekordów z poziomu widoku tabeli danych
  * różnorodna wizualizacja struktur danych (html table, ExtJS Grid ...)

## Struktura i model ##

Ogólna struktura klas składa się z:

TODO: wstawić diagram UML




## Typy danych ##

### Adaptery danych ###

TODO: rozwinąć opisy

KontorX\_DataGrid\_Adapter*** Interface
  * Abstract
  * DbTable
  * DbSelect
  * DbTableTree**


TODO: opisać drobne sztuczki z adapterami (przykłady zastosowań)

### Rodzaje wizualizacji danych ###

KontorX\_DataGrid\_Adapter**_* Interface
  * Abstract
  * HtmlTable - natywna tabela HTML
  * ExtGrid\_Array
  * ExtGrid\_Json_

TODO: pokazać wizualizacje
TODO: opisać plusy i minusy
TODO: przykłądowy kod implementacji**


### KontorX\_DataGrid\_Filter**_###_

#### KontorX\_DataGrid\_Filter\_Text ####**

options:
  * correlationName - string - nazwa korelacji baz danych , tabela z której wartość ma być filtrowana
  * mappedColumn - string - nazwa kolumny, po której ma się odbywać filtrowanie

Przykład:

Zend\_Db\_Select przekazywany do KontorX\_DataGrid::factory

```
$select = new Zend_Db_Select($db);

		$select->from(array('sp' => 'shop_product'))
			   ->join(
			   		array('sm' => 'shop_manufacturer'),
			   			'sp.manufacturer_id = sm.id', array('manufacturer' => 'name'));
```

Wynik zapytania jaki jest generowany

```
SELECT `sp`.*, `sm`.`name` AS `manufacturer` FROM `shop_product` AS `sp` INNER JOIN `shop_manufacturer` AS `sm` ON sp.manufacturer_id = sm.id 
```

Konfiguracja kolumny z korelacją

```
            <name type="Order" name="Nazwa produktu">
                <filter type="Text">
                	<options correlationName="sp"></options>
                </filter>
                <cell type="Html">
					<content><![CDATA[
						<a class="action edit small" href="/shop/product/edit/id/{id}">{name}</a>
						<small class="right blur"><b class="action attach small">alias:</b> {alias}</small>
					]]></content>
                </cell>
            </name>
```