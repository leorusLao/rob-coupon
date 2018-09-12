# rob-coupon
抢券功能的两种实现方式

为了防止出现优惠券超抢的情况
采取了两种解决思路

第一种 beyond.php
利用事务和mysql update 的并发串行化
在减库存时根据返回的影响行数进行判断

第二种 receive
利用redis list pop 的原子性
直接判断库存
