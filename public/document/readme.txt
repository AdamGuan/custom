�汾:v1

url+sign����(����Ϊα����):

	//$keyΪ��ȡ��key
	$key;

	//$urlΪҪ�����api url
	$url;

	//$paramesΪ��POST��GET����Ĳ�������
	//$parames����Ӧ����һ��name����, ����ֵ:����ǿͷ���Ϊ�ͷ���(�����е��˻���)��������û���Ϊ�û���(�����е��˻���)
	$parames;
	
	//����ksort �����鰴��key����
	ksort($parames);
	
	//����http_build_query ��װ����$paramesΪurl��������ʽ�� a=1&b=2&c=3 ����
	$str = http_build_query($parames);	
	
	//md5���ܵõ�sign
	$sign = md5($str."&key=".$key);
	
	//�õ�url
	$url = $url."?sign=".$sign
========================================================================================

(�û�/�ͷ� ʹ��) (signҪ)
д����Ϣ��
	��ַ:
		'http://115.28.232.58/custom/c_api/msg';
	����ʽ:
		POST
	����:
		from		string		�����û���(˭����)
		to		string		�����û���(˭����)
		msg_id:		string		�������ɵ���ϢID
		timestamp:	string		���ŷ���Ϣ���ص�ʱ��������룩
		who_to_who:	int		1:�ͷ������û�,0:�û������ͷ�
	����:
		json��ʽ
			{
				"responseNo":int
			}

		responseNo	�������:
			[0|-1]	0д��ɹ���-1д��ʧ��
	����˵��:

========================================================================================

(�û�ʹ��) (signҪ)
�û����ͷ����ۣ�
	��ַ:
		'http://115.28.232.58/custom/c_api/grade';
	����ʽ:
		POST
	����:
		from		string		�û�(�����û���)
		to		string		�ͷ�(�����û���)
		score:		float		���۷���
		content:	string		��������
	����:
		json��ʽ
			{
				"responseNo":int
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
	����˵��:

========================================================================================

(�û�ʹ��) (signҪ)
�û�����һ���ͷ���
	��ַ:
		'http://115.28.232.58/custom/c_api/find';
	����ʽ:
		POST
	����:
		local		string		����(�磺�㶫 or ����)
	����:
		json��ʽ
			{
				"responseNo":int,
				"custom_name":string,
				"custom_nickname":string,
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		custom_name	�ͷ��������û�����
		custom_nickname	�ͷ��ǳ�
	����˵��:
		�����Ȳ��Ҷ�Ӧ���������е�һ���ͷ������û���κ����ߣ����������һ����Ӧ�����Ŀͷ�������������û���κ�һ���ͷ��򷵻�-1

========================================================================================

(�û�ʹ��)
��ȡ�û���Ϣ��
	��ַ:
		'http://115.28.232.58/custom/c_api/user_info';
	����ʽ:
		POST
	����:
		username		string		�û���(�û����ĵ��Ǹ�email��ַ�˺�)
		userpwd		 	string		�û�����(�û����ĵ��Ǹ�32λ����)
	����:
		json��ʽ
			{
				"responseNo":INT,
				"username":string,
				"userpwd":string,
				"key":string,
				"user_avatar_url":string
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		username	�û��˻�
		userpwd		�û�����
		key		����sign��
		user_avatar_url	�û�ͷ��url
	����˵��:
		
		1 .���û��ע��ͻ�ע��󷵻��û���Ϣ
		2. ���ע���˾�ֱ�ӷ����û���Ϣ��

========================================================================================

(�û�ʹ��) (signҪ)
��ȡ�����б�
	��ַ:
		'http://115.28.232.58/custom/c_api/local_list';
	����ʽ:
		GET/POST
	����:
	����:
		json��ʽ
			{
				"responseNo":INT,
				"local_list":[],
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		local_list	�����б�,һάarray
	����˵��:

========================================================================================

(�ͷ�ʹ��) (signҪ)
�޸Ŀͷ��ǳƣ�
	��ַ:
		'http://115.28.232.58/custom/c_api/modify_custom_nickname';
	����ʽ:
		GET/POST
	����:
		custom_name	string	�ͷ���(�����û���)
		custom_nickname	string	�ͷ��ǳ�
	����:
		json��ʽ
			{
				"responseNo":INT,
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
	����˵��:
		custom_nickname �ǿգ����ȴ��ڵ�¼1 ��С�ڵ���20

========================================================================================

(�ͷ�ʹ��) (signҪ)
�޸Ŀͷ����룺
	��ַ:
		'http://115.28.232.58/custom/c_api/modify_custom_pwd';
	����ʽ:
		GET/POST
	����:
		custom_name		string	�ͷ���(�����û���)
		custom_pwd_current	string	�ͷ���ǰ����
		custom_pwd_new		string	�ͷ�������
	����:
		json��ʽ
			{
				"responseNo":INT,
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
	����˵��:
		custom_pwd_new���� /^[0-9a-zA-Z\-_]{6,20}$/i

========================================================================================

(�ͷ�ʹ��)
��ȡ�ͷ���Ϣ��
	��ַ:
		'http://115.28.232.58/custom/c_api/custom_info';
	����ʽ:
		GET/POST
	����:
		custom_name	string	�ͷ���(�����û���)
		custom_pwd	string	�ͷ�����
	����:
		json��ʽ
			{
				"responseNo":INT,
				"custom_name":string,
				"custom_nickname":string,
				"local":string,
				"key":string,
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		custom_name	�ͷ��� (�����û���)
		custom_nickname	�ͷ��ǳ�
		local		��������
		key		����sign��
	����˵��:
		custom_pwd_new���� /^[0-9a-zA-Z\-_]{6,20}$/i

========================================================================================

(�ͷ�ʹ��) (signҪ)
��ӿͷ����û��Ĺ�ϵ��
	��ַ:
		'http://115.28.232.58/custom/c_api/add_custom_relation';
	����ʽ:
		GET/POST
	����:
		custom_name	string	�ͷ���(�����û���)
		user_name	string	�û���(�����û���)
	����:
		json��ʽ
			{
				"responseNo":INT
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
	����˵��:
	
========================================================================================

(�ͷ�ʹ��) (signҪ)
ɾ���ͷ����û��Ĺ�ϵ��
	��ַ:
		'http://115.28.232.58/custom/c_api/delete_custom_relation';
	����ʽ:
		GET/POST
	����:
		custom_name	string	�ͷ���(�����û���)
		user_name	string	�û���(�����û���)
	����:
		json��ʽ
			{
				"responseNo":INT
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
	����˵��:
	
========================================================================================

(�ͷ�ʹ��) (signҪ)
��ѯ�ͷ����û��Ĺ�ϵ��
	��ַ:
		'http://115.28.232.58/custom/c_api/get_custom_relation';
	����ʽ:
		GET/POST
	����:
		custom_name	string	�ͷ���(�����û���)
	����:
		json��ʽ
			{
				"responseNo":INT,
				"list":[{"add_datetime":datetime,"user_name":string,"register_name":string}]
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		list		��ͻ��й�ϵ���û��б���ά��
			add_datetime	���ʱ��
			user_name		�û���(�����û���)
			register_name	ע����
	����˵��:
	
========================================================================================

(�ͷ�ʹ��) (signҪ)
�ͷ���ѯ�û�����Ϣ��
	��ַ:
		'http://115.28.232.58/custom/c_api/get_other_user_info';
	����ʽ:
		GET/POST
	����:
		user	string	�û���(�����û���)
	����:
		json��ʽ
			{
				"responseNo":INT,
				"user_register_name":string,
				"user_avatar_url":string
			}

		responseNo	�������:
			[0|-1]	0�ɹ���-1ʧ��
		user_register_name		�û�ע����(email��ַ)
		user_avatar_url			�û�ͷ��url
	����˵��:
	
========================================================================================
