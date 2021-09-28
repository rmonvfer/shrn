# shrn
Share your Shodan queries!

_Project made for the Web Software and Standards course (fall semester 20/21) at the University of Oviedo_

It showcases a simple social network where users can post Shodan queries along with a description and also comment and vote on other's publications.
Because of the specific requirements of the subject an import/export functionality is available, allowing registered 
users to download their published queries using an XML-based formated (namely SQSF (_Shareable Shodan Query Format_)) whose
XML schema and DTD are included in the source tree.

Other functionality such as client-side XML validation and common database operations (CRUD only) are also included.


:warning: **DISCLAIMER** :warning:

I know that most of the codebase:
 - Is incomplete and under-documented
 - Uses old technologies (like WTF, who builds serious websites like this?)
 - Highly vulnerable to XSS (both reflected and stored), SQLi and many others
 - Poorly designed
 - Partialy translated
 
Keep in mind that these are the results of some requirements I had to meet in order to pass the subject, so please, don't take it seriously!
