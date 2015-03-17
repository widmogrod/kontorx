# English #
## Description ##

I will try translate this text to english, sometime..., now please use e.g. translate.google.com ;)

## TODO ##
  * more practical examples http://kontorx.widmogrod.info/
  * documentation from polish to english


---


# Polski #

Przyznaję się, używam go i to bardzo często :)

&lt;wiki:gadget url="http://www.ohloh.net/p/20902/widgets/project\_users.xml?style=green" height="100" border="0"/&gt;

## Opis ##

Zestaw klas rozszerzających możliwości Zend Framework:

**KontorX\_DataGrid** – biblioteka umożliwia elastyczne prezentowanie danych tabelarycznych w dowolny sposób  i prawie w dowolnej formie. Poniżej przedstawiam główne cechy biblioteki:
  * Adaptowanie danych różnych typów np. Zend\_Db\_Table, Zend\_Db\_Select, natywna tablica - array, ….
  * Różnorodna forma prezentacji danych. Data\_Grid prezentuje dane jako czysty HTML oraz dynamiczny widok ExtJS Grid. Biblioteka pozwala również na implementacje nowych sposobów prezentacji danych np. jako plików .cvs, .xls, .pdf.
  * Integracja z Zend\_Form.
  * Zbiór gotowych rozwiązań. Biblioteka posiada już zaimplementowane elementy odpowiedzialne za filtrowanie, grupowanie i stronicowanie danych.
  * Elastyczność i rozszerzalność poprzez dopisywanie plugin'ów.

```
// prosty przykład
$dataGrid = KontorX_DataGrid::factory($dbTable, $options);
$dataGrid->render();
```

**KontorX\_Update\_Manager** – rozwój oprogramowania pociąga za sobą ciągłe zmiany oprogramowania, które należy mieć pod kontrolą! Manager aktualizacji jest elastycznym narzędziem, które posiada zaimplementowaną obsługę aktualizacji (i dezaktualizacji)  bazy danych, struktury plików... oraz pozwala w szybki sposób zaimplementować obsługę nowych zestawów narzędzi aktualizacyjnych.

```
// prosty przykład
$manager = new KontorX_Update_Manager($pathToDirWithUpdates);
$result = $manager->update(); // bool
```

**KontorX\_Ftp** - biblioteka unifikuje (i w niektórych przypadkach wzbogaca) interfejs w sposób funkcjonalny czyli: nawiązywania połączeń, czy operacje na plikach,... .

```
// prosty przykład
$ftp = KontorX_Ftp::factory('ftp', array(
	'server' => 'ftp.widmogrod.info',
	'username' => 'non_user',
	'password' => 'non_password'
));
$ftp->ls(); // return array of file names
```

**KontorX\_Db\_Table\_Tree** – stworzenie i zarządzanie hierarchiczną strukturą danych w MySQL nie jest możliwe w naturalny sposób (precyzyjniej: brak rekurencji w MySQL nie pozwala na zbudowanie i zwrócenie struktury drzewiastej). Rozwiązanie tego problemu jest możliwe poprzez programistyczne podejście do tego zagadnienia. Właśnie do tego celu powstała  ta biblioteka.

Dodatkowo można w bardzo prosty sposób przetworzyć KontorX\_Db\_Table\_Tree\_Rowset na Zend\_Navigation\_Container za pomocą KontorX\_Navigation\_Recursive + Promotor\_Navigation\_Recursive\_Visitor\_Site np.:

```
// prosty przykład
$navigation = new KontorX_Navigation_Recursive($rowsetTree);
$navigation->accept(new Promotor_Navigation_Recursive_Visitor_Site());
return $navigation->create(); // Zend_Navigation_Container
```

**KontorX\_Search\_Semantic** – jest to implementacja wzorca projektowego „interpreter”. Głównym zadaniem tej biblioteki jest proste „rozumienie” tekstu poprzez rozbijanie przekazanego ciągu znaków na logiczne składowe.

**KontorX\_Controller\_Action\_Scaffold** – często powtarzające się operacje tworzenia, edycji i usuwania rekordów w bazie danych zostały wyabstrahowane do zewnętrznej klasy dzięki czemu następuje poprawa jakości projektowania aplikacji gdyż można skoncentrować się na bardziej wymagających zagadnieniach.

  * KontorX\_Template - ... //todo
  * KontorX\_Image - ... //todo
  * KontorX\_JavaScript - ... //todo
  * KontorX\_Gwt - ... //todo
  * KontorX\_Calendar - ... //todo
  * KontorX\_Archive - ... //todo
  * KontorX\_Observable - ... //todo

**KontorX\_Form**:
  * KontorX\_Form\_Element\_NIP - ... //todo
  * KontorX\_Form\_Element\_DataGrid - ... //todo
  * KontorX\_Form\_Element\_SelectTree - ... //todo
  * KontorX\_Form\_Element\_DateTime - ... //todo
  * KontorX\_Form\_Element\_Date - ... //todo

## TODO ##
  * więcej praktycznych przykładów http://kontorx.widmogrod.info/
  * dokumentacja całkowicie po angielsku