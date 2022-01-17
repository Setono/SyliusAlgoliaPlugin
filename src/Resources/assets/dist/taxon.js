"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = _default;

var _lite = _interopRequireDefault(require("algoliasearch/lite"));

var _instantsearch = _interopRequireDefault(require("instantsearch.js"));

var _widgets = require("instantsearch.js/es/widgets");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _default() {
  var algoliaCredentials = document.getElementById('algolia-credentials');
  var algoliaIndex = document.getElementById('algolia-index').dataset.value;
  var algoliaTaxon = document.getElementById('algolia-taxon').dataset.value;
  var algoliaHitTemplate = document.getElementById('algolia-hit-template').innerHTML;
  var searchClient = (0, _lite["default"])(algoliaCredentials.dataset.appId, algoliaCredentials.dataset.searchApiKey);
  var search = (0, _instantsearch["default"])({
    indexName: algoliaIndex,
    searchClient: searchClient,
    routing: true
  });
  search.addWidgets([(0, _widgets.hits)({
    container: '#hits',
    templates: {
      item: algoliaHitTemplate
    }
  }), (0, _widgets.rangeSlider)({
    container: '#range-slider',
    attribute: 'price'
  }), (0, _widgets.hierarchicalMenu)({
    container: '#hierarchical-menu',
    attributes: ['taxons.lvl0', 'taxons.lvl1', 'taxons.lvl2'],
    showParentLevel: false
  }), (0, _widgets.configure)({
    filters: "taxonCodes:".concat(algoliaTaxon)
  })]);
  search.start();
}