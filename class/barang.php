<?php 
class Barang {
    private $_db = null;
    private $_formItem = [];

    public function __construct() 
    {
        $this->_db = DB::GetInstance();
    }

    public function validasi($formMethod){
        $validate = new Validate($formMethod);

        $this->_formItem['Kode'] = $validate->setRules('Kode','Kode',
    [
        
        'required' => true,
        'minchar' => 4,
        'regexp' => '/[a-zA-Z][0-9]+/',
    ]);
    $this->_formItem['NamaBarang'] = $validate->setRules('NamaBarang','Nama Barang',
    [
        
        'required' => true,
    ]);
    $this->_formItem['Jumlah'] = $validate->setRules('Jumlah','Jumlah',
    [
        'required' => true,
        'numeric' => true
    ]);
    $this->_formItem['Harga'] = $validate->setRules('Harga','Harga',
    [
        'required' => true,
        'numeric' => true
    ]);
    $this->_formItem['Jual'] = $validate->setRules('Jual','Jual',
    [
        'required' => true,
        'numeric' => true
    ]);
    
    if (!$validate->passed()){
        return $validate->getError();
    
    }
    
   
}
public function validasijual($formMethod){
    $validatejual = new Validate($formMethod);

    $this->_formItem['Nama'] = $validatejual->setRules('Nama','Nama',
[
    
    'required' => true,
]);
$this->_formItem['NamaBarang'] = $validatejual->setRules('NamaBarang','Nama Barang',
[
    
    'required' => true,
]);
$this->_formItem['Jumlah'] = $validatejual->setRules('Jumlah','Jumlah',
[
    'required' => true,
    'numeric' => true
]);
$this->_formItem['HargaJual'] = $validatejual->setRules('HargaJual','HargaJual',
[
    'required' => true,
    'numeric' => true
]);
$this->_formItem['HargaBeli'] = $validatejual->setRules('HargaBeli','HargaBeli',
[
    'required' => true,
    'numeric' => true
]);
$check_kredit = $this->_formItem['cbkredit'] = $validatejual->setRules('cbkredit','Kredit',
[
    'required' => true,
    
   
]);
$check_cash = $this->_formItem['cbcash'] =$validatejual->setRules('cbcash','Cash',
[
    
    
   
]);
$tgl=$this->_formItem['tgl'] = $validatejual->setRules('tgl','Tanggal',
[
    'required' => true,
]);
$bln=$this->_formItem['bln'] = $validatejual->setRules('bln','Bulan',
[
    'required' => true,
]);
$thn=$this->_formItem['thn'] = $validatejual->setRules('thn','Tahun',
[
    'required' => true,
]);

if (!$validatejual->passed()){
    return $validatejual->getError();

}
}
public function validasibeli($formMethod){
    $validatebeli = new Validate($formMethod);

$this->_formItem['NamaBarang'] = $validatebeli->setRules('NamaBarang','Nama Barang',
[
    
    'required' => true,
]);
$this->_formItem['Jumlah'] = $validatebeli->setRules('Jumlah','Jumlah',
[
    'required' => true,
    'numeric' => true
]);
$this->_formItem['HargaBeli'] = $validatebeli->setRules('HargaBeli','HargaBeli',
[
    'required' => true,
    'numeric' => true
]);

if (!$validatebeli->passed()){
    return $validatebeli->getError();

}

}
public function getItem($item){
    return isset($this->_formItem[$item]) ? $this->_formItem[$item] : '';
}

public function insert() {
    $newBarang = [ 'Id' => $this->getItem('Id'),
                    'Kode' => $this->getItem('Kode'),
                  'NamaBarang'=> $this->getItem('NamaBarang'),
                  'Jumlah' => $this->getItem('Jumlah'),
                  'Harga' => $this->getItem('Harga'),
                  'Jual' => $this->getItem('Jual')];
    return $this->_db->insert('barang',$newBarang);
}

public function generate($kode){
    $result=$this->_db->getWhereOnce('barang',['Kode','=',$kode]);
    foreach ($result as $key => $val){
        $this->_formItem[$key] = $val;
    }
}
    public function update($idbarang){
        $newBarang = ['Kode' => $this->getItem('Kode'),
                      'NamaBarang' => $this->getItem('NamaBarang'),
                      'Jumlah' => $this->getItem('Jumlah'),
                      'Harga' => $this->getItem('Harga'),
                      'Jual' => $this->getItem('Jual')];
        $this->_db->update('barang',$newBarang,['Id','=',$this->getItem('Id')]);
    }
    public function delete($kode){
        $this->_db->delete('barang',['Kode','=',$kode]);
    }
    public function jual(){
        $tanggal = $this->getItem('tgl');
        $bulan =$this->getItem('bln');
        $tahun =$this->getItem('thn');
        $date = "$tanggal-$bulan-$tahun";   
        $newJual = ['Nama' => $this->getItem('Nama'),
                    'NamaBarang' => $this->getItem('NamaBarang'),
                    'Jumlah' => $this->getItem('Jumlah'),
                    'HargaJual' => $this->getItem('HargaJual'),
                    'HargaBeli' => $this->getItem('HargaBeli'),
                    'JenisPembayaran' => $this->getItem('cbkredit')|$this->getItem('cbcash'),
                    'Tanggal' => $date
    ];
        return $this->_db->insert('penjualan',$newJual);
    }
    
    public function beli(){
        $newBeli = [
                    'NamaBarang' => $this->getItem('NamaBarang'),
                    'Jumlah' => $this->getItem('Jumlah'),                   
                    'HargaBeli' => $this->getItem('HargaBeli'),                    
    ];
        return $this->_db->insert('pembelian',$newBeli);    
    }
}