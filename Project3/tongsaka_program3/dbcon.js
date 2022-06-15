var mysql = require('mysql');
var pool = mysql.createPool({
  connectionLimit : 10,
  host            : 'classmysql.engr.oregonstate.edu', 
  user            : 'cs340_tongsaka',
  password        : '2702',
  database        : 'cs340_tongsaka'
});

module.exports.pool = pool;
