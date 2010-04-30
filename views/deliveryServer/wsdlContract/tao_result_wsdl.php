<?php
include_once(dirname(__FILE__).'../../../../generis/common/config.php');
$pathToResultServer= ROOT_URL.'/taoDelivery/views/deliveryServer/resultServer';

$wsdl='
<?xml version="1.0" encoding="UTF-8"?>
<wsdl:definitions
    name="tao"
    targetNamespace="urn:tao"
    xmlns="http://schemas.xmlsoap.org/wsdl/"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:si="http://soapinterop.org/xsd"
    xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:tns="urn:tao"
    xmlns:typens="urn:tao"
    xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <wsdl:types>
        <xsd:schema
            targetNamespace="urn:tao"
            xmlns="http://schemas.xmlsoap.org/wsdl/"
            xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:si="http://soapinterop.org/xsd"
            xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
            xmlns:tns="urn:tao"
            xmlns:typens="urn:tao"
            xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <xsd:complexType name="ArrayOfstring">
                <xsd:complexContent>
                    <xsd:restriction base="SOAP-ENC:Array">
                        <xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="xsd:string[]"/>
                    </xsd:restriction>
                </xsd:complexContent>
            </xsd:complexType>
          
        </xsd:schema>
    </wsdl:types>
<wsdl:message name="setResultRequest">
<wsdl:part name="pResultDS" type="tns:ArrayOfstring" /> 
<wsdl:part name="pResultID" type="tns:ArrayOfstring" /> 
<wsdl:part name="pResultSQ" type="tns:ArrayOfstring" /> 
<wsdl:part name="pResultNB" type="tns:ArrayOfstring" /> 
</wsdl:message>
   
    <wsdl:message name="setResultResponse">
        <wsdl:part name="pResult" type="tns:ArrayOfstring"/>
    </wsdl:message>

<wsdl:message name="isFullyOkRequest">
<wsdl:part name="IDresult" type="tns:ArrayOfstring" /> 
<wsdl:part name="numberElts" type="tns:ArrayOfstring" /> 
</wsdl:message>
   
    <wsdl:message name="isFullyOkResponse">
        <wsdl:part name="pResult" type="tns:ArrayOfstring"/>
    </wsdl:message>

    <wsdl:portType name="TAO_PortType">
        <wsdl:operation name="setResult">
            <documentation>Request to connect to the TAO system</documentation>
            <wsdl:input message="tns:setResultRequest"/>
            <wsdl:output message="tns:setResultResponse"/>
        </wsdl:operation>
		<wsdl:operation name="isFullyOk">
            <documentation>Request to connect to the TAO system</documentation>
            <wsdl:input message="tns:isFullyOkRequest"/>
            <wsdl:output message="tns:isFullyOkResponse"/>
        </wsdl:operation>
    </wsdl:portType>

<wsdl:binding name="TAO_Binding" type="tns:TAO_PortType">
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>

        <wsdl:operation name="setResult">
            <soap:operation
               soapAction="'.$pathToResultServer.'/Uploadresultserver.php"
                style="rpc"/>
            <wsdl:input>
                <soap:body
                    encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
                    namespace="urn:tao"
                    use="encoded"/>
            </wsdl:input>
            <wsdl:output>
                <soap:body
                    encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
                    namespace="urn:tao"
                    use="encoded"/>
            </wsdl:output>
        </wsdl:operation>
		
		<wsdl:operation name="isFullyOk">
            <soap:operation
               soapAction="'.$pathToResultServer.'/uploadResultServer.php"
                style="rpc"/>
            <wsdl:input>
                <soap:body
                    encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
                    namespace="urn:tao"
                    use="encoded"/>
            </wsdl:input>
            <wsdl:output>
                <soap:body
                    encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
                    namespace="urn:tao"
                    use="encoded"/>
            </wsdl:output>
        </wsdl:operation>

    </wsdl:binding>
    <wsdl:service name="Uploadresult">
        <wsdl:port binding="tns:TAO_Binding" name="tao_UploadresultPort">
            <soap:address location="'.$pathToResultServer.'/uploadResultServer.php"/>
        </wsdl:port>
	</wsdl:service>

</wsdl:definitions>';

echo $wsdl;
?>
