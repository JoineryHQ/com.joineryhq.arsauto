# CiviCRM: ARS Auto
Performs automatic calculation of certain data points for ARS (the sponsor of this extension).

This is an [extension for CiviCRM](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/), licensed under [GPL-3.0](LICENSE.txt).

## What it does

This extension defines and populates certain custom fields based on certain logic.
All these custom fields are read-only, thus ensuring they only contain auto-calculated
values.

### Fields under Custom Data Group "ARS Contact Attributes (Calculated)"

This custom group is defined for all contacts, and contains these fields

- **Region: District**: Contains a string in the format "[Region]: [District]", where 
  Region is auto-calculated from District, and District is auto-calculated from the State
  and Postal Code values in the contact's Primary address, according to a hard-coded
  set of rules defined by ARS staff and encoded into this extension.
  
  
### Scheduled Jobs

This extension provides an API, `Arsauto.update`, and a Scheduled Job configured to call
this API with a frequency of "always".  This API will, in batches of up to 5000, update the
auto-calculated custom fields for all contacts not yet having such a value.

This is expected to be useful only in the first few hours after installing the extension,
in order to initialize the auto-calculated values for all contacts. Thereafter, each contact
will have the correct values calculated in real-time as data is modified.  Therefore, it's
recommended to consider disabling this scheduled job after the first day or so, since it's
probably not needed.


## Note on re-enabling this extension
This extension creates several entities (Custom Data fields, Option Groups and Option Values,
etc.) in order to do its work. Those entities are automatically disabled when the extension
is disabled. Unlike most such "managed entities" defined by other extensions, most of this
extension's managed entities will be automatically re-enabled when the extension is re-enabled.
The sole exception is the Scheuled Job "Call Arsauto.Update API".


## Configuration
This extension calls for no configuration; it merely does its job.

## Support

Support for this plugin is handled under Joinery's ["As-Is Support" policy](https://joineryhq.com/software-support-levels#as-is-support).

Public issue queue for this plugin: https://github.com/JoineryHQ/com.joineryhq.arsauto/issues