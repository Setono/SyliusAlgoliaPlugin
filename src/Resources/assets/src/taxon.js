import algoliasearch from 'algoliasearch/lite';
import instantsearch from 'instantsearch.js';
import { hits, rangeSlider, hierarchicalMenu, configure } from 'instantsearch.js/es/widgets';

export default function() {
    const algoliaCredentials = document.getElementById('algolia-credentials');
    const algoliaIndex = document.getElementById('algolia-index').dataset.value;
    const algoliaTaxon = document.getElementById('algolia-taxon').dataset.value;
    const algoliaHitTemplate = document.getElementById('algolia-hit-template').innerHTML;

    const searchClient = algoliasearch(algoliaCredentials.dataset.appId, algoliaCredentials.dataset.searchApiKey);

    const search = instantsearch({
        indexName: algoliaIndex,
        searchClient,
        routing: true,
    });

    search.addWidgets([
        hits({
            container: '#hits',
            templates: {
                item: algoliaHitTemplate,
            }
        }),
        rangeSlider({
            container: '#range-slider',
            attribute: 'price',
        }),
        hierarchicalMenu({
            container: '#hierarchical-menu',
            attributes: [
                'taxons.lvl0',
                'taxons.lvl1',
                'taxons.lvl2',
            ],
            showParentLevel: false,
        }),
        configure({
            filters: `taxonCodes:${algoliaTaxon}`
        })
    ]);

    search.start();
}
