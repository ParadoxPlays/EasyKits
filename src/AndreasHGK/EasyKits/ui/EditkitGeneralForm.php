<?php
/**
 *    _____                         _  __  _   _         
 *   | ____|   __ _   ___   _   _  | |/ / (_) | |_   ___ 
 *   |  _|    / _` | / __| | | | | | ' /  | | | __| / __|
 *   | |___  | (_| | \__ \ | |_| | | . \  | | | |_  \__ \
 *   |_____|  \__,_| |___/  \__, | |_|\_\ |_|  \__| |___/
 *                           |___/                        
 *          by AndreasHGK and fernanACM 
 */
declare(strict_types=1);

namespace AndreasHGK\EasyKits\ui;

use AndreasHGK\EasyKits\Kit;
use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;

use pocketmine\player\Player;

use Vecnavium\FormsUI\CustomForm;

class EditkitGeneralForm {

    public static function sendTo(Player $player, Kit $kit) : void {

        $ui = new CustomForm(function (Player $player, $data) use ($kit) {
            if($data === null) {
                EditkitMainForm::sendTo($player, $kit);
                return;
            }

            if(!isset($data["name"])) {
                $player->sendMessage(LangUtils::getMessage("editkit-general-no-name"));
                return;
            }

            if(KitManager::exists((string)$data["name"]) && $kit->getName() !== (string)$data["name"]) {
                $player->sendMessage(LangUtils::getMessage("editkit-general-duplicate"));
                return;
            }

            if(!is_float((float)$data["price"])) {
                $player->sendMessage(LangUtils::getMessage("editkit-general-invalid-price"));
                return;
            }

            if(!is_int((int)$data["cooldown"])) {
                $player->sendMessage(LangUtils::getMessage("editkit-general-invalid-cooldown"));
                return;
            }

            $new = clone $kit;

            $new->setName((string)$data["name"]);
            $new->setPrice((float)$data["price"]);
            $new->setCooldown((int)$data["cooldown"]);
            $new->setLocked($data["locked"]);
            $new->setEmptyOnClaim($data["emptyOnClaim"]);
            $new->setDoOverride($data["doOverride"]);
            $new->setDoOverrideArmor($data["doOverrideArmor"]);
            $new->setAlwaysClaim($data["alwaysClaim"]);
            $new->setChestKit($data["chestKit"]);
            $new->setPermission($data["permission"] ?? (string)$data["name"]);

            if(KitManager::update($kit, $new, true)) {
                KitManager::saveAll();
                $player->sendMessage(LangUtils::getMessage("editkit-success", true, ["{NAME}" => $kit->getName()]));
            }
            return;
        });
        $ui->setTitle(LangUtils::getMessage("editkit-title"));
        $ui->addLabel(LangUtils::getMessage("editkit-general-text"));

        $ui->addInput(LangUtils::getMessage("editkit-general-kitname"), "", $kit->getName(), "name");
        $ui->addInput(LangUtils::getMessage("editkit-general-permission"), "", $kit->getPermission(), "permission");
        $ui->addInput(LangUtils::getMessage("editkit-general-price"), "", (string)$kit->getPrice(), "price");
        $ui->addInput(LangUtils::getMessage("editkit-general-cooldown"), "", (string)$kit->getCooldown(), "cooldown");
        $ui->addLabel(LangUtils::getMessage("editkit-general-flags"));
        $ui->addToggle(LangUtils::getMessage("editkit-general-lockedToggle"), $kit->isLocked(), "locked");
        $ui->addToggle(LangUtils::getMessage("editkit-general-emptyOnClaimToggle"), $kit->emptyOnClaim(), "emptyOnClaim");
        $ui->addToggle(LangUtils::getMessage("editkit-general-doOverrideToggle"), $kit->doOverride(), "doOverride");
        $ui->addToggle(LangUtils::getMessage("editkit-general-doOverrideArmorToggle"), $kit->doOverrideArmor(), "doOverrideArmor");
        $ui->addToggle(LangUtils::getMessage("editkit-general-alwaysClaimToggle"), $kit->alwaysClaim(), "alwaysClaim");
        $ui->addToggle(LangUtils::getMessage("editkit-general-chestKitToggle"), $kit->isChestKit(), "chestKit");

        $player->sendForm($ui);
    }

}