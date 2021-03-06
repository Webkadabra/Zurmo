<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2013 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2013. All rights reserved".
     ********************************************************************************/

    class CustomFieldsTest extends ZurmoBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
        }

        public function testAccountAndContactIndustries()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $values = array(
                'Automotive',
                'Adult Entertainment',
                'Financial Services',
                'Mercenaries & Armaments',
            );
            $industryFieldData = CustomFieldData::getByName('Industries');
            $industryFieldData->defaultValue = $values[0];
            $industryFieldData->serializedData = serialize($values);
            $this->assertTrue($industryFieldData->save());
            unset($industryFieldData);

            $user = UserTestHelper::createBasicUser('Billy');

            $account = new Account();
            $account->name  = 'Consoladores-R-Us';
            $account->owner = $user;
            $data = unserialize($account->industry->data->serializedData);
            $this->assertEquals('Automotive', $account->industry->value);
            $account->industry->value = $values[1];
            $this->assertTrue($account->save());
            unset($account);

            ContactsModule::loadStartingData();
            $states = ContactState::GetAll();
            $contact = new Contact();
            $contact->firstName = 'John';
            $contact->lastName  = 'Johnson';
            $contact->owner     = $user;
            $contact->state     = $states[0];
            $values = unserialize($contact->industry->data->serializedData);
            $this->assertEquals(4, count($values));
            $contact->industry->value = $values[3];
            $this->assertTrue($contact->save());
            unset($contact);

            $accounts = Account::getByName('Consoladores-R-Us');
            $account  = $accounts[0];
            $this->assertEquals('Adult Entertainment', $account->industry->value);

            $contacts = Contact::getAll();
            $contact  = $contacts[0];
            $this->assertEquals('Mercenaries & Armaments', $contact->industry->value);
        }
    }
?>
