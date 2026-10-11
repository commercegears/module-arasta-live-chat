<?php
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Test\Unit;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use PHPUnit\Framework\TestCase;
use CommerceGears\ArastaLiveChat\Model\Config\Source\CustomerPhoneAttribute;

class CustomerPhoneAttributeTest extends TestCase
{
    private function attribute(string $code, string $label, string $input, bool $visible = true, string $backend = 'varchar'): AttributeMetadataInterface
    {
        $a = $this->createMock(AttributeMetadataInterface::class);
        $a->method('getAttributeCode')->willReturn($code);
        $a->method('getFrontendLabel')->willReturn($label);
        $a->method('getFrontendInput')->willReturn($input);
        $a->method('isVisible')->willReturn($visible);
        $a->method('getBackendType')->willReturn($backend);
        return $a;
    }

    /** The merchant picks a visible free-text attribute (phone, mobile, telephone…); names, internal account fields and non-text attributes are not offered */
    public function testListsTextAttributesExceptKnownNonPhoneOnes(): void
    {
        $metadata = $this->createMock(CustomerMetadataInterface::class);
        $metadata->method('getAllAttributesMetadata')->willReturn([
            $this->attribute('email', 'Email', 'text'),
            $this->attribute('mobile_phone', 'Mobile phone', 'text'),
            $this->attribute('dob', 'Date of birth', 'date'),
            $this->attribute('telephone', 'Telephone', 'text'),
            // Magento's internal account fields use text inputs too (QA 2026-10-11): never offered.
            $this->attribute('confirmation', 'Is Confirmed', 'text', false, 'static'),
            $this->attribute('default_billing', 'Default Billing Address', 'text', true, 'static'),
            $this->attribute('created_in', 'Created From', 'text'),
            $this->attribute('hidden_note', 'Hidden note', 'text', false),
            $this->attribute('loyalty_points', 'Loyalty points', 'text', true, 'int'),
        ]);
        $options = (new CustomerPhoneAttribute($metadata))->toOptionArray();
        self::assertSame(['', 'mobile_phone', 'telephone'], array_column($options, 'value'));
        self::assertSame('Mobile phone (mobile_phone)', $options[1]['label']);
    }
}
