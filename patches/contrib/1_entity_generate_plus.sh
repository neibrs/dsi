#!/usr/bin/env bash


# 2020/04/30 23:41:27 [error] 432#0: *845 FastCGI sent in stderr:
# "PHP message: ArgumentCountError: Too few arguments to function Drupal\import\Plugin\migrate\process\EntityGeneratePlus::__construct(),
# 3 passed in /Users/nie/Projects/drupal/modules/contrib/migrate_plus/src/Plugin/migrate/process/EntityLookup.php on line 152 and
# exactly 7 expected in /Users/nie/Projects/drupal/modules/eabax/core/import/src/Plugin/migrate/process/EntityGeneratePlus.php on line 32
# 0 /Users/nie/Projects/drupal/modules/contrib/migrate_plus/src/Plugin/migrate/process/EntityLookup.php(152):
# Drupal\import\Plugin\migrate\process\EntityGeneratePlus->__construct(Array, 'entity_generate', Array)
# #1 /Users/nie/Projects/drupal/modules/contrib/migrate_plus/src/Plugin/migrate/process/EntityGenerate.php(78):
# Drupal\migrate_plus\Plugin\migrate\process\EntityLookup::create(Object(Drupal\Core\DependencyInjection\Container), Array, 'entity_generate', Array, Object(Drupal\import\Plugin\Migration))
# #2 /Users/nie/Projects/drupal/core/modules/migrate/src/Plugin/MigratePluginManager.php(57):
# Drupal\migra" while reading response header from upstream, client: 127.0.0.1, server: dsi.server.host, request: "POST /batch?id=4&op=do_nojs&op=do&_format=json HTTP/1.1", upstream: "fastcgi://127.0.0.1:9100", host: "dsi.server.host:88", referrer: "http://dsi.server.host:88/batch?id=4&op=start"

