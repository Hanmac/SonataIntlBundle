<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sonata\IntlBundle\Helper\DateTimeFormatter;
use Sonata\IntlBundle\Helper\Localizer;
use Sonata\IntlBundle\Helper\NumberFormatter;
use Sonata\IntlBundle\Locale\RequestDetector;
use Sonata\IntlBundle\Locale\RequestStackDetector;
use Sonata\IntlBundle\Timezone\ChainTimezoneDetector;
use Sonata\IntlBundle\Timezone\LocaleAwareBasedTimezoneDetector;
use Sonata\IntlBundle\Timezone\LocaleBasedTimezoneDetector;
use Sonata\IntlBundle\Timezone\UserBasedTimezoneDetector;
use Sonata\IntlBundle\Twig\DateTimeRuntime;
use Sonata\IntlBundle\Twig\Extension\DateTimeExtension;
use Sonata\IntlBundle\Twig\Extension\LocaleExtension;
use Sonata\IntlBundle\Twig\Extension\NumberExtension;
use Sonata\IntlBundle\Twig\LocaleRuntime;
use Sonata\IntlBundle\Twig\NumberRuntime;
use Sonata\IntlBundle\Util\BCDeprecationParameters;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1

    $containerConfigurator->parameters()

        ->set('sonata.intl.locale_detector.request.class', RequestDetector::class)
        ->set('sonata.intl.helper.locale.class', Localizer::class)
        ->set('sonata.intl.helper.number.class', NumberFormatter::class)
        ->set('sonata.intl.helper.datetime.class', DateTimeFormatter::class)
        ->set('sonata.intl.timezone_detector.chain.class', ChainTimezoneDetector::class)
        ->set('sonata.intl.timezone_detector.user.class', UserBasedTimezoneDetector::class)
        ->set('sonata.intl.timezone_detector.locale.class', LocaleBasedTimezoneDetector::class)
        ->set('sonata.intl.timezone_detector.locale_aware.class', LocaleAwareBasedTimezoneDetector::class)
        ->set('sonata.intl.twig.helper.locale.class', LocaleExtension::class)
        ->set('sonata.intl.twig.helper.number.class', NumberExtension::class)
        ->set('sonata.intl.twig.helper.datetime.class', DateTimeExtension::class);

    $containerConfigurator->services()

        ->set('sonata.intl.locale_detector.request', '%sonata.intl.locale_detector.request.class%')
            ->public()
            ->deprecate(...BCDeprecationParameters::forConfig(
                'The "%service_id%" service is deprecated since sonata-project/intl-bundle 2.8 and will be removed in 3.0.',
                '2.8'
            ))
            ->args([
                new ReferenceConfigurator('service_container'),
                '',
            ])

        ->set('sonata.intl.locale_detector.request_stack', RequestStackDetector::class)
            ->public()
            ->deprecate(...BCDeprecationParameters::forConfig(
                'The "%service_id%" service is deprecated since sonata-project/intl-bundle 2.13 and will be removed in 3.0.',
                '2.13'
            ))
            ->args([
                new ReferenceConfigurator('request_stack'),
                '',
            ])

        ->set('sonata.intl.helper.locale', '%sonata.intl.helper.locale.class%')
            ->public()
            ->args([
                '%kernel.charset%',
            ])

        ->set('sonata.intl.helper.number', '%sonata.intl.helper.number.class%')
            ->public()
            ->args([
                '%kernel.charset%',
            ])

        ->set('sonata.intl.helper.datetime', '%sonata.intl.helper.datetime.class%')
            ->public()
            ->args([
                new ReferenceConfigurator('sonata.intl.timezone_detector'),
                '%kernel.charset%',
            ])

        ->set('sonata.intl.twig.extension.locale', '%sonata.intl.twig.helper.locale.class%')
            ->private()
            ->tag('twig.extension')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.locale'),
            ])

        ->set('sonata.intl.twig.extension.number', '%sonata.intl.twig.helper.number.class%')
            ->private()
            ->tag('twig.extension')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.number'),
            ])

        ->set('sonata.intl.twig.extension.datetime', '%sonata.intl.twig.helper.datetime.class%')
            ->private()
            ->tag('twig.extension')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.datetime'),
            ])

        ->set('sonata.intl.twig.runtime.locale', LocaleRuntime::class)
            ->tag('twig.runtime')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.locale'),
            ])

        ->set('sonata.intl.twig.runtime.number', NumberRuntime::class)
            ->tag('twig.runtime')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.number'),
            ])

        ->set('sonata.intl.twig.runtime.datetime', DateTimeRuntime::class)
            ->tag('twig.runtime')
            ->args([
                new ReferenceConfigurator('sonata.intl.helper.datetime'),
            ])

        ->set('sonata.intl.timezone_detector.chain', '%sonata.intl.timezone_detector.chain.class%')
            ->public()
            ->args([
                '',
            ])

        ->set('sonata.intl.timezone_detector.user', '%sonata.intl.timezone_detector.user.class%')
            ->public()
            ->tag('sonata_intl.timezone_detector', [
                'alias' => 'user',
            ])
            ->args([
                new ReferenceConfigurator('security.token_storage'),
            ])

        ->set('sonata.intl.timezone_detector.locale', '%sonata.intl.timezone_detector.locale.class%')
            ->public()
            ->deprecate(...BCDeprecationParameters::forConfig(
                'The "%service_id%" service is deprecated since sonata-project/intl-bundle 2.13 and will be removed in 3.0.',
                '2.13'
            ))
            ->tag('sonata_intl.timezone_detector', [
                'alias' => 'locale',
            ])
            ->args([
                new ReferenceConfigurator('sonata.intl.locale_detector'),
                '',
            ])

        ->set('sonata.intl.timezone_detector.locale_aware', '%sonata.intl.timezone_detector.locale_aware.class%')
            ->public()
            ->tag('sonata_intl.timezone_detector', [
                'alias' => 'locale_aware',
            ])
            ->args([
                '',
            ]);
};
