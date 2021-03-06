<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\Tests\LinkAccessibility\LinkQuality;

use PHPUnit\Framework\TestCase;
use P1ho\AccessibilityChecker\LinkAccessibility\LinkQuality\Checker;

final class CheckerTest extends TestCase
{
    public function testEvaluation(): void
    {
        // test #, mailto:, and tel:
        $this->assertEquals(
            ['is_redirect'    => false,
             'is_dead'        => false,
             'is_same_domain' => false,
             'timed_out'      => false],
            Checker::evaluate('#something', 'https://www.google.com')
        );
        $this->assertEquals(
            ['is_redirect'    => false,
             'is_dead'        => false,
             'is_same_domain' => false,
             'timed_out'      => false],
            Checker::evaluate('mailto:something', 'https://www.google.com')
        );
        $this->assertEquals(
            ['is_redirect'    => false,
             'is_dead'        => false,
             'is_same_domain' => false,
             'timed_out'      => false],
            Checker::evaluate('tel:something', 'https://www.google.com')
        );

        // special case: dead link if using HEAD request
        $this->assertEquals(
            ['is_redirect'    => false,
             'is_dead'        => false,
             'is_same_domain' => false,
             'timed_out'      => false],
            Checker::evaluate('https://www.instagram.com/umichunions/', 'https://www.google.com')
        );
       
        // special case: redirect
        $this->assertEquals(
            ['is_redirect'    => true,
             'is_dead'        => false,
             'is_same_domain' => false,
             'timed_out'      => false],
            Checker::evaluate('http://packagist.org/', 'https://www.google.com')
        );
      
        // special case: same domain
        $this->assertEquals(
            ['is_redirect'    => true,
             'is_dead'        => false,
             'is_same_domain' => true,
             'timed_out'      => false],
            Checker::evaluate('http://packagist.org/', 'https://packagist.org/')
        );
        $this->assertEquals(
            ['is_redirect'    => false,
             'is_dead'        => false,
             'is_same_domain' => true,
             'timed_out'      => false],
            Checker::evaluate('https://packagist.org/', 'https://packagist.org/')
        );
    }

    public function testGetSiteUrl(): void
    {
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com')
      );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/')
      );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something')
      );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something')
      );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something/else')
      );
        $this->assertEquals(
            'https://www.google.com',
            Checker::get_site_url('https://www.google.com/something/else/')
      );
    }

    public function testComputeLinkUrl(): void
    {
        $this->assertEquals('http://example.com', Checker::compute_link_url(
            '//example.com',
            '/some/page/',
            'https://www.google.com'
       ));
        $this->assertEquals('https://www.google.com/this/is/a/path', Checker::compute_link_url(
            '/this/is/a/path',
            '/some/page/',
            'https://www.google.com'
       ));
        $this->assertEquals('http://example.com', Checker::compute_link_url(
            'http://example.com',
            '/some/page/',
            'https://www.google.com'
       ));
        $this->assertEquals('https://example.com', Checker::compute_link_url(
            'https://example.com',
            '/some/page/',
            'https://www.google.com'
       ));
        $this->assertEquals('https://www.google.com/some/page/./this/is/a/path', Checker::compute_link_url(
            './this/is/a/path',
            '/some/page/',
            'https://www.google.com'
       ));
        $this->assertEquals('https://www.google.com/some/page/../this/is/a/path', Checker::compute_link_url(
            '../this/is/a/path',
            '/some/page/',
            'https://www.google.com'
       ));
    }
}
