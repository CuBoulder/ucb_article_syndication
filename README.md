# CU Boulder Article Syndication

This Drupal module extends the article content type to add the taxonomies
needed for article syndication. This is needed on the 
[CU Boulder Today](https://www.colorado.edu/today/) site (and possibly a small
number of other sites). Simply install the module and everything should just
work after that. The
[Campus News](https://webexpress.colorado.edu/article/637-campus-news-block)
block requires these taxonomies to be present.

**Taxonomies added:**

- Audience (syndication_audience)
- Unit (syndication_unit)

**Important:** This module updates the article content type's form display, so
the configuration can get overriden with the one in the
[custom entities](https://github.com/CuBoulder/tiamat-custom-entities) module.
If this happens, run the `showSyndicationFields` function of the
`ucb_article_syndication` service manually to restore the changes.
