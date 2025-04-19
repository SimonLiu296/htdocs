# 游戏账号数据管理系统

## 项目概述
这是一个基于PHP和Layui框架开发的游戏账号数据管理系统，主要用于管理游戏账号的各类数据，包括蜡烛、爱心、季蜡和升华等资源的统计和管理。系统采用现代化的UI设计，提供了完整的数据管理功能，包括数据的增删改查、送心关系管理以及详细的操作日志记录。

## 主要功能

### 1. 用户认证系统
- 安全的登录/退出功能
- 会话管理和权限控制
- 美观的登录界面，支持粒子动画效果
- 用户操作日志记录

### 2. 数据总览功能
- 实时显示账号总数统计
- 账号数据表格展示，支持排序
- 数据实时刷新功能
- 分页显示和搜索功能

### 3. 数据管理功能
- 添加新账号数据
  * 支持账号备注
  * 自动记录创建时间
  
- 修改现有数据
  * 支持修改蜡烛数量
  * 支持修改爱心数量
  * 支持修改季蜡数量
  * 支持修改升华数量
  * 自动记录更新时间

- 删除数据
  * 支持删除账号
  * 支持删除老板
  * 删除确认机制
  * 关联数据处理

### 4. 送心管理功能
- 送心数据查看
  * 显示送心总数统计
  * 显示老板总数统计
  * 支持按老板筛选
  
- 送心数据添加
  * 支持添加新老板
  * 支持多选账号
  * 支持搜索选择
  * 批量添加功能

### 5. 操作日志功能
- 记录所有操作类型
- 记录操作详情
- 记录操作人员
- 记录操作时间
- 支持分页查看

## 技术架构

### 前端技术
- Layui v2.x：UI框架
- jQuery v3.7.1：JavaScript库
- xm-select：下拉多选组件
- particles.js：粒子动画效果
- ECharts：数据可视化图表

### 后端技术
- PHP 7.0+：服务器端语言
- MySQL 5.6+：数据库系统
- PDO：数据库操作接口
- Apache/Nginx：Web服务器

### 数据库设计
1. accounts表（账号表）
```sql
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remark VARCHAR(255) NOT NULL COMMENT '账号备注',
    candles INT UNSIGNED DEFAULT 0 COMMENT '蜡烛数量',
    hearts INT UNSIGNED DEFAULT 0 COMMENT '爱心数量',
    season_candles INT UNSIGNED DEFAULT 0 COMMENT '季蜡数量',
    ascended_candles INT UNSIGNED DEFAULT 0 COMMENT '升华数量',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

2. bosses表（老板表）
```sql
CREATE TABLE bosses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT '老板名称',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

3. heart_relations表（送心关系表）
```sql
CREATE TABLE heart_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boss_id INT NOT NULL,
    account_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (boss_id) REFERENCES bosses(id),
    FOREIGN KEY (account_id) REFERENCES accounts(id)
)
```

4. operation_logs表（操作日志表）
```sql
CREATE TABLE operation_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_type VARCHAR(50) NOT NULL COMMENT '操作类型',
    operation_detail TEXT COMMENT '操作详情',
    operator VARCHAR(50) NOT NULL COMMENT '操作人',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

## 系统要求
- PHP 7.0 或更高版本
- MySQL 5.6 或更高版本
- Apache 2.4 或 Nginx 1.12 或更高版本
- 现代浏览器（Chrome、Firefox、Edge等）
- 最小分辨率：1024x768

## 安装部署

### 1. 环境准备
- 安装XAMPP/WAMP或其他PHP开发环境
- 确保PHP开启PDO扩展
- 确保MySQL服务正常运行

### 2. 项目部署
1. 将项目文件放置在Web服务器根目录下
2. 导入数据库结构：
   ```bash
   php init_db.php
   ```
3. 配置数据库连接：
   编辑 `config/database.php` 文件，修改以下配置：
   ```php
   $host = 'localhost';
   $dbname = 'candle_system';
   $username = 'root';
   $password = '123456';
   ```
4. 优化数据库：
   ```bash
   mysql -u root -p < add_indexes.sql
   mysql -u root -p < optimize_tables.sql
   ```
5. 设置目录权限：
   ```bash
   chmod 755 -R /path/to/project
   chmod 777 -R /path/to/project/logs
   ```

### 3. 访问系统
- 打开浏览器访问：`http://localhost/`
- 默认登录信息请联系系统管理员

## 目录结构
```
├── api/                # API接口目录
│   ├── login.php      # 登录接口
│   ├── get_*.php      # 数据获取接口
│   ├── add_*.php      # 数据添加接口
│   ├── update_*.php   # 数据更新接口
│   └── delete_*.php   # 数据删除接口
├── config/            # 配置文件目录
│   └── database.php   # 数据库配置
├── images/           # 图片资源目录
│   ├── bg.jpg        # 登录背景
│   └── head.jpg      # 用户头像
├── js/               # JavaScript文件目录
│   ├── jquery-*.js   # jQuery库
│   ├── echarts.js    # 图表库
│   └── particles.js  # 粒子效果
├── layui/            # Layui框架目录
│   ├── css/          # 样式文件
│   ├── font/         # 字体文件
│   └── layui.js      # 核心JS
├── add.php           # 添加数据页面
├── delete.php        # 删除数据页面
├── edit.php          # 编辑数据页面
├── heart_add.php     # 添加送心页面
├── heart_view.php    # 查看送心页面
├── index.php         # 系统首页
├── init_db.php       # 数据库初始化
├── login.php         # 登录页面
├── logout.php        # 退出登录
├── overview.php      # 数据总览页面
└── logs.php          # 操作记录页面
```

## 功能优化
1. 数据库优化
   - 添加了适当的索引
   - 优化了表结构
   - 使用了合适的字段类型
   - 添加了外键约束

2. 性能优化
   - 使用了数据库连接池
   - 实现了分页查询
   - 优化了SQL查询
   - 添加了缓存机制

3. 安全优化
   - 实现了登录验证
   - 防止SQL注入
   - 防止XSS攻击
   - 数据输入验证

4. 界面优化
   - 响应式设计
   - 现代化UI
   - 友好的交互
   - 合理的布局

## 维护说明
1. 日常维护
   - 定期检查系统日志
   - 监控系统性能
   - 定期数据备份
   - 清理过期数据

2. 故障处理
   - 检查错误日志
   - 验证数据完整性
   - 恢复数据备份
   - 修复系统问题

## 更新记录
### v1.0.0 (2024-04-19)
- 初始版本发布
- 实现基础功能
- 完成系统架构
- 优化用户体验

## 联系方式
- 开发者：晨风
- 邮箱：请联系管理员
- QQ：请联系管理员

## 版权信息
Copyright © 2024-2025 数据管理系统 All Rights Reserved 