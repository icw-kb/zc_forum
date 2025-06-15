Plugins are zip files 

they may be stored in the filesytem or in a remote location(e.g.) AWS 
for now we assume they are stored in the filesystem

See the migration for the current schema 

e.g. `2023_10_01_000000_create_plugins_table.php`

THis should be taken as a starting point

Things that will need to be added:

Plugins must belong to a group 
e.g. 'admin', 'payments', 'analytics'

plugins will need a link to a github repository although this is not strictly necessary

plugins can be viewed by anyone 

plugins can only be downloaded by logged in user 

plugins can only be uplaoded by logged in users 

for the fronten we need to be able too search for plugins
we can  list plugins by group or some othe way of categorization to be decided later 

we need to keep statistics for plugins e.g number of views/downloads

pkugins can be tagged with zen cart versions, this might be multiple versions
(possibly use a taggable propert for this and a standard larvel package)




