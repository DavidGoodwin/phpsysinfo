<?php
/**
 * coretemp sensor class, getting hardware temperature information through sysctl on FreeBSD
 * or from /sys/devices/platform/coretemp. on Linux
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @author    William Johansson <radar@radhuset.org>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Coretemp extends Hwmon
{
    /**
     * get the information
     *
     * @see PSI_Interface_Sensor::build()
     *
     * @return void
     */
    public function build()
    {
        if ((PSI_OS == 'Linux') && !defined('PSI_EMU_HOSTNAME')) {
            $hwpaths = glob("/sys/devices/platform/coretemp.*/", GLOB_NOSORT);
            if (is_array($hwpaths) && (count($hwpaths) > 0)) {
                $hwpaths2 = glob("/sys/devices/platform/coretemp.*/hwmon/hwmon*/", GLOB_NOSORT);
                if (is_array($hwpaths2) && (count($hwpaths2) > 0)) {
                    $hwpaths = array_merge($hwpaths, $hwpaths2);
                }
                $totalh = count($hwpaths);
                for ($h = 0; $h < $totalh; $h++) {
                    $this->_temperature($hwpaths[$h]);
                }
            }
        } elseif (PSI_OS == 'FreeBSD') {
            $smp = 1;
            CommonFunctions::executeProgram('sysctl', '-n kern.smp.cpus', $smp);
            for ($i = 0; $i < $smp; $i++) {
                $temp = 0;
                if (CommonFunctions::executeProgram('sysctl', '-n dev.cpu.'.$i.'.temperature', $temp)) {
                    $temp = preg_replace('/,/', '.', preg_replace('/C/', '', $temp));
                    $dev = new SensorDevice();
                    $dev->setName("CPU ".($i + 1));
                    $dev->setValue($temp);
//                    $dev->setMax(70);
                    $this->mbinfo->setMbTemp($dev);
                }
            }
        }
    }
}
