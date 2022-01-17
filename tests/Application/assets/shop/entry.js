import 'sylius/bundle/ShopBundle/Resources/private/entry';
import init from '@setono/sylius-algolia-plugin/dist/taxon';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
