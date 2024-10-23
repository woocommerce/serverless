valkey-server /usr/local/etc/valkey/valkey.conf --daemonize yes && sleep 2
valkey-cli FLUSHALL
si_val='{"id":1,"key":"test", "url":"'
si_val=$si_val$HOST
si_val=$si_val'"}'
valkey-cli SET si:site1.wb.test "$si_val"
valkey-cli SET wh:test '{"id":"site_wb_test","url":"site1.wb.test"}'
valkey-cli SAVE
valkey-cli shutdown

valkey-server /usr/local/etc/valkey/valkey.conf
